<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function dashboard()
    {
        // Statistiques de base
        $totalSpots = ParkingSpot::count();
        $activeSpots = ParkingSpot::where('is_active', true)->count();
        $occupiedSpots = Reservation::where('status', 'active')->count();
        $waitingUsers = WaitingList::where('status', 'waiting')->count();
        $totalUsers = User::count();

        $stats = [
            'total_spots' => $totalSpots,
            'active_spots' => $activeSpots,
            'occupied_spots' => $occupiedSpots,
            'waiting_users' => $waitingUsers,
            'total_users' => $totalUsers,
        ];

        // Calcul des pourcentages pour les graphiques
        $percentages = [
            'occupation' => $totalSpots > 0 ? round(($occupiedSpots / $totalSpots) * 100) : 0,
            'disponibilite' => $totalSpots > 0 ? round((($activeSpots - $occupiedSpots) / $totalSpots) * 100) : 0,
            'inactives' => $totalSpots > 0 ? round((($totalSpots - $activeSpots) / $totalSpots) * 100) : 0,
        ];

        // Données pour le graphique d'évolution des réservations sur les 7 derniers jours
        $lastWeekReservations = [];
        $lastWeekLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Reservation::whereDate('created_at', $date)->count();
            $lastWeekReservations[] = $count;
            $lastWeekLabels[] = now()->subDays($i)->format('d/m');
        }

        // Données pour le graphique de répartition des statuts des réservations
        $reservationStatuses = [
            'active' => Reservation::where('status', 'active')->count(),
            'closed' => Reservation::where('status', 'closed')->count(),
        ];

        $latestReservations = Reservation::with(['user', 'parkingSpot'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestReservations',
            'percentages',
            'lastWeekReservations',
            'lastWeekLabels',
            'reservationStatuses'
        ));
    }

    public function users()
    {
        $users = User::withCount(['reservations', 'waitingList'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function reservations(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        // Compteurs pour les onglets
        $activeCount = Reservation::where('status', 'active')->count();
        $closedCount = Reservation::where('status', 'closed')->count();
        $totalCount = $activeCount + $closedCount;

        $query = Reservation::with(['user', 'parkingSpot']);

        if ($status === 'active') {
            $query->where('status', 'active');
        } elseif ($status === 'closed') {
            $query->where('status', 'closed');
        }

        // Recherche par nom d'utilisateur ou numéro de place
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('parkingSpot', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
            });
        }

        $reservations = $query
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'closed' THEN 1 ELSE 2 END")
            ->orderBy('ends_at', 'desc')
            ->paginate(20)
            ->appends(['status' => $status, 'search' => $search]);

        return view('admin.reservations', compact('reservations', 'status', 'activeCount', 'closedCount', 'totalCount', 'search'));
    }

    public function createUser(Request $request)
    {
        Log::info('Tentative de création d\'utilisateur', $request->all());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,admin'],
        ]);

        Log::info('Données validées', $validated);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'is_active' => true,
            ]);

            Log::info('Utilisateur créé avec succès', ['user_id' => $user->id]);
            return back()->with('status', 'Utilisateur créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'utilisateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de la création de l\'utilisateur.');
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Utilisateur mis à jour avec succès.');
    }

    public function resetUserPassword(User $user)
    {
        $password = Str::random(12);

        $user->update([
            'password' => Hash::make($password)
        ]);

        return back()->with('password', "Nouveau mot de passe : $password");
    }
}
