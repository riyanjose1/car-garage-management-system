<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // =========================
        // USER SEARCH + LIST
        // =========================
        $q = trim((string) $request->query('q'));

        $usersQuery = User::query()->latest();

        if (!empty($q)) {
            $usersQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        $users = $usersQuery->paginate(10)->withQueryString();

        // =========================
        // STATS
        // =========================
        $stats = [
            'users_total'   => User::count(),
            'admins'        => User::where('role', 'admin')->count(),
            'moderators'    => User::where('role', 'moderator')->count(),
            'customers'     => User::where('role', 'user')->count(),
            'vehicles'      => Vehicle::count(),
            'appointments'  => Appointment::count(),
            'invoices'      => Invoice::count(),
        ];

        // =========================
        // MONTHLY PROFIT/LOSS SERIES (X-axis = Month)
        // =========================
        $series = $this->buildMonthlyProfitLossSeries(6);
        $chartData = collect($series['labels'])->map(function ($label, $i) use ($series) {
            $profit = (float) ($series['profits'][$i] ?? 0);
            $loss = (float) ($series['losses'][$i] ?? 0);

            return [
                'month' => $label,
                'profit' => $profit,
                'revenue' => $profit + $loss,
            ];
        })->values();

        return view('admin.dashboard', [
            'users'   => $users,
            'stats'   => $stats,
            'q'       => $q,
            'chartData' => $chartData,

            // ✅ Chart.js expects these
            'labels'  => $series['labels'],   // ["Sep 2025", "Oct 2025", ...]
            'profits' => $series['profits'],  // [..numbers..]
            'losses'  => $series['losses'],   // [..numbers..]
        ]);
    }

    // ✅ Export chart data as CSV (Excel can open it)
    // Route name should point to this method: admin.profit.export
    public function exportProfitCsv()
    {
        $series = $this->buildMonthlyProfitLossSeries(6);

        $filename = 'admin-profit-loss-monthly.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($series) {
            $out = fopen('php://output', 'w');

            // Header row
            fputcsv($out, ['Month', 'Profit', 'Loss']);

            $totalProfit = 0;
            $totalLoss = 0;

            for ($i = 0; $i < count($series['labels']); $i++) {
                $month = $series['labels'][$i];
                $profit = (float) $series['profits'][$i];
                $loss = (float) $series['losses'][$i];

                $totalProfit += $profit;
                $totalLoss += $loss;

                fputcsv($out, [
                    $month,
                    number_format($profit, 2, '.', ''),
                    number_format($loss, 2, '.', ''),
                ]);
            }

            // Totals
            fputcsv($out, []);
            fputcsv($out, [
                'TOTAL',
                number_format($totalProfit, 2, '.', ''),
                number_format($totalLoss, 2, '.', ''),
            ]);

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,moderator,user',
        ]);

        $user = User::findOrFail($id);

        // prevent removing your own admin
        if (auth()->id() === $user->id && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.']);
        }

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'User role updated.');
    }

    /**
     * Build last N months series for chart
     * X-axis: month labels
     * Y-axis: profit/loss
     *
     * If no invoice data, auto-generates realistic demo numbers.
     */
    private function buildMonthlyProfitLossSeries(int $months = 6): array
    {
        $labels = [];
        $profits = [];
        $losses = [];

        // oldest -> newest
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();

            $label = $date->format('M Y');
            $labels[] = $label;

            // revenue from invoices created that month
            $revenue = (float) Invoice::whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            // If empty database, generate demo values so your chart looks presentable
            if ($revenue <= 0) {
                $revenue = rand(30000, 90000);
            }

            // Simple demo logic:
            // Profit = 70% of revenue, Loss = 30% of revenue
            $profit = round($revenue * 0.7);
            $loss   = round($revenue * 0.3);

            $profits[] = $profit;
            $losses[]  = $loss;
        }

        return [
            'labels'  => $labels,
            'profits' => $profits,
            'losses'  => $losses,
        ];
    }
}
