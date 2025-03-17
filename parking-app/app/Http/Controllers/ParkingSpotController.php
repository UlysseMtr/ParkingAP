<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use App\Models\WaitingList;

class ParkingSpotController extends Controller
{
    public function index()
    {
        $parkingSpots = ParkingSpot::with('currentReservation.user')->get();
        return view('parking-spots.index', compact('parkingSpots'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $validated = $request->validate([
            'number' => ['required', 'string', 'unique:parking_spots,number,NULL,id,deleted_at,NULL'],
            'description' => ['nullable', 'string'],
        ]);

        ParkingSpot::create($validated);
        return back()->with('status', 'Place de parking créée avec succès.');
    }

    public function update(Request $request, ParkingSpot $parkingSpot)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $validated = $request->validate([
            'number' => ['required', 'string', 'unique:parking_spots,number,' . $parkingSpot->id . ',id,deleted_at,NULL'],
            'description' => ['nullable', 'string'],
        ]);

        $parkingSpot->update($validated);
        return back()->with('status', 'Place de parking mise à jour avec succès.');
    }

    public function destroy(ParkingSpot $parkingSpot)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        if ($parkingSpot->currentReservation) {
            return back()->with('error', 'Impossible de supprimer une place actuellement réservée.');
        }

        $parkingSpot->forceDelete();
        return back()->with('status', 'Place de parking supprimée avec succès.');
    }

    public function updatePositions(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'positions' => ['required', 'array'],
            'positions.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->positions as $id => $position) {
            WaitingList::where('id', $id)->update(['position' => $position]);
        }

        return back()->with('status', 'Positions mises à jour avec succès.');
    }
}
