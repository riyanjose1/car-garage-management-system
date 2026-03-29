<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
        ]);

        Vehicle::create([
            'user_id' => auth()->id(),
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'plate_number' => strtoupper(trim($request->plate_number)),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Vehicle added successfully.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        // Ownership protection
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
        ]);

        $vehicle->update([
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'plate_number' => strtoupper(trim($request->plate_number)),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Ownership protection
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $vehicle->delete();

        return redirect()->route('user.dashboard')->with('success', 'Vehicle deleted successfully.');
    }
}
