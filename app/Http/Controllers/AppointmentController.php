<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // ============================
    // USER: CREATE BOOKING
    // ============================
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'   => 'required|exists:vehicles,id',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'service_type' => 'required|string|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Ensure logged-in user owns the vehicle
        $vehicle = auth()->user()->vehicles()
            ->where('id', $request->vehicle_id)
            ->firstOrFail();

        Appointment::create([
            'user_id'      => auth()->id(),
            'vehicle_id'   => $vehicle->id,
            'service_date' => $request->service_date,
            'service_time' => $request->service_time,
            'service_type' => $request->service_type,
            'notes'        => $request->notes,
            'status'       => 'Pending',
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Service booking created.');
    }

    // ============================
    // MODERATOR: LIST BOOKINGS (filters/search/pagination)
    // ============================
    public function moderatorIndex(Request $request)
{
    $q = trim((string) $request->query('q'));

    $appointmentsQuery = Appointment::with(['user', 'vehicle'])
        ->latest();

    if (!empty($q)) {
        $appointmentsQuery->where(function ($query) use ($q) {

            // Search by user
            $query->whereHas('user', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            })

            // Search by vehicle
            ->orWhereHas('vehicle', function ($v) use ($q) {
                $v->where('brand', 'like', "%{$q}%")
                  ->orWhere('model', 'like', "%{$q}%")
                  ->orWhere('plate_number', 'like', "%{$q}%");
            })

            // Search by status
            ->orWhere('status', 'like', "%{$q}%");
        });
    }

    $appointments = $appointmentsQuery
        ->paginate(10)
        ->withQueryString();

    return view('moderator.appointments.index', [
        'appointments' => $appointments,
        'q' => $q,
    ]);
}

    // ============================
    // MODERATOR: UPDATE STATUS + CREATE INVOICE ON COMPLETED
    // ============================
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Completed,Cancelled',
            'labor_cost' => 'nullable|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        // If marking as completed AND invoice does not exist -> create invoice
        if ($request->status === 'Completed' && !$appointment->invoice) {

            $labor = (float) ($request->labor_cost ?? 0);
            $parts = (float) ($request->parts_cost ?? 0);
            $total = $labor + $parts;

            // Generate invoice number like: INV-20260220-0001
            $today = now()->format('Ymd');
            $sequence = str_pad((string) (Invoice::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-{$today}-{$sequence}";

            Invoice::create([
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user_id,
                'invoice_number' => $invoiceNumber,
                'labor_cost' => $labor,
                'parts_cost' => $parts,
                'total_amount' => $total,
            ]);
        }

        return back()->with('success', 'Booking status updated.');
    }

    // ============================
    // MODERATOR: CALENDAR (WEEK VIEW)
    // ============================
    public function calendar(Request $request)
    {
        $week = $request->query('week'); // YYYY-MM-DD

        $start = $week
            ? Carbon::parse($week)->startOfWeek()
            : Carbon::now()->startOfWeek();

        $end = $start->copy()->endOfWeek();

        $appointments = Appointment::with(['user', 'vehicle', 'invoice'])
            ->whereBetween('service_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('service_date')
            ->orderBy('service_time')
            ->get()
            ->groupBy('service_date');

        return view('moderator.calendar', [
            'start' => $start,
            'end' => $end,
            'appointments' => $appointments,
        ]);
    }

    // ============================
    // MODERATOR: DOWNLOAD INVOICE PDF
    // ============================
    public function downloadInvoicePdf(Invoice $invoice)
    {
        // Security: only moderator/admin
        if (!in_array(auth()->user()->role, ['moderator', 'admin'])) {
            abort(403);
        }

        $invoice->load([
            'appointment.user',
            'appointment.vehicle'
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice
        ])->setPaper('a4', 'portrait');

        $fileName = ($invoice->invoice_number ?: ('Invoice-' . $invoice->id)) . '.pdf';

        return $pdf->download($fileName);
    }
}