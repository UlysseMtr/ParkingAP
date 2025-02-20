<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingSpotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WaitingListController;
use App\Http\Middleware\AdminMiddleware;

// Routes publiques
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées par auth
Route::middleware('auth')->group(function () {
    // Gestion du profil
    Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.update');

    // Gestion des places de parking
    Route::get('/parking-spots', [ParkingSpotController::class, 'index'])->name('parking-spots.index');

    // Gestion des réservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/close', [ReservationController::class, 'close'])->name('reservations.close');

    // Gestion de la liste d'attente
    Route::get('/waiting-list', [WaitingListController::class, 'index'])->name('waiting-list.index');
    Route::post('/waiting-list/cancel', [WaitingListController::class, 'cancel'])->name('waiting-list.cancel');

    // Routes d'administration
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Gestion des utilisateurs
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::post('/admin/users', [AdminController::class, 'createUser'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::post('/admin/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.reset-password');

        // Gestion des places de parking
        Route::post('/admin/parking-spots', [ParkingSpotController::class, 'store'])->name('admin.parking-spots.store');
        Route::put('/admin/parking-spots/{parkingSpot}', [ParkingSpotController::class, 'update'])->name('admin.parking-spots.update');
        Route::delete('/admin/parking-spots/{parkingSpot}', [ParkingSpotController::class, 'destroy'])->name('admin.parking-spots.destroy');

        // Gestion de la liste d'attente
        Route::put('/admin/waiting-list/positions', [WaitingListController::class, 'updatePositions'])->name('admin.waiting-list.update-positions');
    });
});
