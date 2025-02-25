<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingSpotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WaitingListController;
use App\Http\Controllers\NotificationController;

// Routes publiques
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées par auth
Route::middleware('auth')->group(function () {
    // Gestion du profil
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');
    Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.update');

    // Gestion des places de parking
    Route::get('/parking-spots', [ParkingSpotController::class, 'index'])->name('parking-spots.index');

    // Gestion des réservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/close', [ReservationController::class, 'close'])->name('reservations.close');

    // Gestion de la liste d'attente
    Route::get('/waiting-list', [WaitingListController::class, 'index'])->name('waiting-list.index');
    Route::post('/waiting-list/join', [WaitingListController::class, 'join'])->name('waiting-list.join');
    Route::post('/waiting-list/cancel', [WaitingListController::class, 'cancel'])->name('waiting-list.cancel');

    // Gestion des notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Routes d'administration
    Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\Admin::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestion des utilisateurs
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');

        // Gestion des places de parking
        Route::post('/parking-spots', [ParkingSpotController::class, 'store'])->name('parking-spots.store');
        Route::put('/parking-spots/{parkingSpot}', [ParkingSpotController::class, 'update'])->name('parking-spots.update');
        Route::delete('/parking-spots/{parkingSpot}', [ParkingSpotController::class, 'destroy'])->name('parking-spots.destroy');

        // Gestion de la liste d'attente
        Route::put('/waiting-list/positions', [WaitingListController::class, 'updatePositions'])->name('waiting-list.update-positions');
    });
});
