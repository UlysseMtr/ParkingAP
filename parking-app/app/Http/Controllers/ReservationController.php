<?php

namespace App\Http\Controllers;

use App\Models\ParkingSpot;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Statistiques pour les réservations
        $stats = [
            'total_reservations' => $user->reservations()->count(),
            'active_reservations' => $user->reservations()->where('status', 'active')->count(),
            'completed_reservations' => $user->reservations()->where('status', 'closed')->count(),
            'current_spot' => $user->reservations()->where('status', 'active')->first()?->parkingSpot,
        ];

        return view('reservations.index', compact('reservations', 'stats'));
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

        // Crée la réservation
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

        // Vérifie si un admin termine la réservation d'un utilisateur ?
        $isAdminClosingForUser = $user->isAdmin() && $reservation->user_id !== $user->id;

        $reservation->close();

        // Si il le fait --> envoyer une notification à l'utilisateur
        if ($isAdminClosingForUser) {
            Notification::create([
                'user_id' => $reservation->user_id,
                'type' => 'reservation_closed_by_admin',
                'message' => 'Un administrateur a terminé votre réservation pour la place n°' . ($reservation->parkingSpot->number ?? 'Supprimée') . '.',
                'data' => [
                    'parking_spot_id' => $reservation->parking_spot_id,
                    'parking_spot_number' => $reservation->parkingSpot ? $reservation->parkingSpot->number : 'Supprimée',
                    'admin_id' => $user->id,
                    'admin_name' => $user->name
                ]
            ]);
        }

        // Vérifier la liste d'attente pour cette place spécifique
        $nextInLine = WaitingList::where('status', 'waiting')
            ->where('parking_spot_id', $reservation->parking_spot_id)
            ->orderBy('position')
            ->first();

        // Si personne n'attend cette place spécifique chercher la première personne en attente
        if (!$nextInLine) {
            $nextInLine = WaitingList::where('status', 'waiting')
                ->orderBy('position')
                ->first();
        }

        if ($nextInLine) {
            // Créer une notification pour l'utilisateur qui obtient la place
            Notification::create([
                'user_id' => $nextInLine->user_id,
                'type' => 'spot_available',
                'message' => 'Une place de parking est maintenant disponible pour vous ! Une réservation a été créée automatiquement.',
                'data' => [
                    'parking_spot_id' => $reservation->parking_spot_id,
                    'parking_spot_number' => $reservation->parkingSpot ? $reservation->parkingSpot->number : 'Supprimée'
                ]
            ]);

            $nextInLine->remove();

            // Créer une nouvelle réservation pour la personne suivante
            Reservation::create([
                'user_id' => $nextInLine->user_id,
                'parking_spot_id' => $nextInLine->parking_spot_id ?? $reservation->parking_spot_id,
                'starts_at' => now(),
                'ends_at' => now()->addHours(24),
                'status' => 'active'
            ]);

            // Notifier les autres utilisateurs de la liste d'attente de leur nouvelle position
            $updatedWaitingList = WaitingList::where('status', 'waiting')
                ->orderBy('position')
                ->get();

            foreach ($updatedWaitingList as $entry) {
                Notification::create([
                    'user_id' => $entry->user_id,
                    'type' => 'position_updated',
                    'message' => "Votre position dans la liste d'attente a été mise à jour. Vous êtes maintenant en position {$entry->position}.",
                    'data' => [
                        'old_position' => $entry->position + 1,
                        'new_position' => $entry->position
                    ]
                ]);
            }
        }

        return back()->with('status', 'Réservation terminée avec succès.');
    }
}
