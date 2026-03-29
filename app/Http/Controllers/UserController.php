<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Appointment;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $vehicles = Vehicle::where('user_id', $user->id)
            ->latest()
            ->get();

        $appointments = Appointment::with('vehicle')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.dashboard', [
            'vehicles' => $vehicles,
            'appointments' => $appointments,
        ]);
    }
}
