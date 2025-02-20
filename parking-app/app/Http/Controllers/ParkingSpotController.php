<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ParkingSpotController extends Controller
{
    public function index()
    {
        $parkingSpots = ParkingSpot::with('currentReservation.user')->get();
        return view('parking-spots.index', compact('parkingSpots'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->isAdmin(), 403);

        $validated = $request->validate([
            'number' => ['required', 'string', 'unique:parking_spots,number'],
            'description' => ['nullable', 'string'],
        ]);

        ParkingSpot::create($validated);
        return back()->with('status', 'Place de parking créée avec succès.');
    }

    public function update(Request $request, ParkingSpot $parkingSpot)
    {
        abort_if(!Auth::user()->isAdmin(), 403);

        $validated = $request->validate([
            'number' => ['required', 'string', 'unique:parking_spots,number,' . $parkingSpot->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $parkingSpot->update($validated);
        return back()->with('status', 'Place de parking mise à jour avec succès.');
    }

    public function destroy(ParkingSpot $parkingSpot)
    {
        abort_if(!Auth::user()->isAdmin(), 403);

        if ($parkingSpot->currentReservation()) {
            return back()->with('error', 'Impossible de supprimer une place actuellement réservée.');
        }

        $parkingSpot->delete();
        return back()->with('status', 'Place de parking supprimée avec succès.');
    }
}
