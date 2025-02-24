<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Affiche la liste des réservations de l'utilisateur.
     *
     * @return View
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $reservations = $user->reservations()->latest()->paginate(10);
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Crée une nouvelle réservation.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasActiveReservation()) {
            return back()->with('error', 'Vous avez déjà une réservation active.');
        }

        if ($user->isInWaitingList()) {
            return back()->with('error', 'Vous êtes déjà dans la liste d\'attente.');
        }

        $validated = $request->validate([
            'parking_spot_id' => ['required', 'exists:parking_spots,id'],
            'duration' => ['required', 'integer', 'in:24,48,72'],
        ]);

        $parkingSpot = ParkingSpot::find($validated['parking_spot_id']);

        if (!$parkingSpot->isAvailable()) {
            return back()->with('error', 'Cette place n\'est plus disponible.');
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_spot_id' => $parkingSpot->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours((int) $validated['duration']),
            'status' => 'active'
        ]);

        return back()->with('status', 'Place de parking n°' . $parkingSpot->number . ' réservée avec succès pour ' . $validated['duration'] . ' heures.');
    }

    /**
     * Termine une réservation.
     *
     * @param Reservation $reservation
     * @return RedirectResponse
     */
    public function close(Reservation $reservation): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($reservation->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $reservation->close();

        // Vérifier la liste d'attente
        $nextInLine = WaitingList::where('status', 'waiting')
            ->orderBy('position')
            ->first();

        if ($nextInLine) {
            $nextInLine->remove();

            // Créer une nouvelle réservation pour la personne suivante
            Reservation::create([
                'user_id' => $nextInLine->user_id,
                'parking_spot_id' => $reservation->parking_spot_id,
                'starts_at' => now(),
                'ends_at' => now()->addHours(24),
                'status' => 'active'
            ]);
        }

        return back()->with('status', 'Réservation terminée avec succès.');
    }
}
