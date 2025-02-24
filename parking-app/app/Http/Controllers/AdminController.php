<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function dashboard()
    {
        $stats = [
            'total_spots' => ParkingSpot::count(),
            'active_spots' => ParkingSpot::where('is_active', true)->count(),
            'occupied_spots' => Reservation::where('status', 'active')->count(),
            'waiting_users' => WaitingList::where('status', 'waiting')->count(),
        ];

        $latestReservations = Reservation::with(['user', 'parkingSpot'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestReservations'));
    }

    public function users()
    {
        $users = User::withCount(['reservations', 'waitingList'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,admin'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return back()->with('status', 'Utilisateur créé avec succès.');
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
