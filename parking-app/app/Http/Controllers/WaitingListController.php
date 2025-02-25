<?php

namespace App\Http\Controllers;

use App\Models\WaitingList;
use App\Models\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controller as BaseController;

class WaitingListController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste d'attente.
     *
     * @return View
     */
    public function index(): View
    {
        $waitingList = WaitingList::with(['user', 'parkingSpot'])
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        $parkingSpots = ParkingSpot::where('is_active', true)->get();

        return view('waiting-list.index', compact('waitingList', 'parkingSpots'));
    }

    /**
     * Ajoute l'utilisateur à la liste d'attente.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function join(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasActiveReservation()) {
            return back()->with('error', 'Vous avez déjà une réservation active.');
        }

        if ($user->isInWaitingList()) {
            return back()->with('error', 'Vous êtes déjà dans la liste d\'attente.');
        }

        $validated = $request->validate([
            'parking_spot_id' => ['required', 'exists:parking_spots,id'],
        ]);

        $parkingSpot = ParkingSpot::find($validated['parking_spot_id']);

        if (!$parkingSpot->is_active) {
            return back()->with('error', 'Cette place n\'est pas disponible.');
        }

        if ($parkingSpot->isAvailable()) {
            return back()->with('error', 'Cette place est actuellement libre. Vous pouvez la réserver directement.');
        }

        WaitingList::create([
            'user_id' => $user->id,
            'parking_spot_id' => $parkingSpot->id,
            'position' => WaitingList::getNextPosition(),
            'requested_at' => now(),
            'status' => 'waiting'
        ]);

        return back()->with('status', 'Vous avez été ajouté à la liste d\'attente pour la place n°' . $parkingSpot->number . '.');
    }

    /**
     * Retire l'utilisateur de la liste d'attente.
     *
     * @return RedirectResponse
     */
    public function cancel(): RedirectResponse
    {
        $waitingListEntry = Auth::user()->waitingList()->where('status', 'waiting')->first();

        if ($waitingListEntry) {
            $waitingListEntry->remove();
            return back()->with('status', 'Vous avez été retiré de la liste d\'attente.');
        }

        return back()->with('error', 'Vous n\'êtes pas dans la liste d\'attente.');
    }

    /**
     * Met à jour les positions dans la liste d'attente.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePositions(Request $request): RedirectResponse
    {
        abort_if(!Auth::user()->isAdmin(), 403);

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
