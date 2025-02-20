<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use App\Models\Reservation;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reservations = Auth::user()->reservations()->latest()->paginate(10);
        return view('reservations.index', compact('reservations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasActiveReservation()) {
            return back()->with('error', 'Vous avez déjà une réservation active.');
        }

        if ($user->isInWaitingList()) {
            return back()->with('error', 'Vous êtes déjà dans la liste d\'attente.');
        }

        // Trouver une place disponible au hasard
        $availableSpot = ParkingSpot::whereDoesntHave('reservations', function ($query) {
            $query->where('status', 'active');
        })->where('is_active', true)->inRandomOrder()->first();

        if (!$availableSpot) {
            // Ajouter à la liste d'attente
            WaitingList::create([
                'user_id' => $user->id,
                'position' => WaitingList::getNextPosition(),
                'requested_at' => now(),
                'status' => 'waiting'
            ]);

            return back()->with('status', 'Aucune place disponible. Vous avez été ajouté à la liste d\'attente.');
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_spot_id' => $availableSpot->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours(24), // Durée par défaut de 24h
            'status' => 'active'
        ]);

        return back()->with('status', 'Place de parking n°' . $availableSpot->number . ' réservée avec succès.');
    }

    public function close(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
