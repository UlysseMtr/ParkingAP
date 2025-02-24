@extends('layouts.app')

@section('title', 'Tableau de bord administrateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places totales</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places actives</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places occupées</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['occupied_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">En attente</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['waiting_users'] }}</dd>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('admin.users.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Gestion des utilisateurs</p>
                    <p class="truncate text-sm text-gray-500">Gérer les comptes utilisateurs</p>
                </div>
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
            </a>
            <a href="{{ route('parking-spots.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Places de parking</p>
                    <p class="truncate text-sm text-gray-500">Gérer les places de parking</p>
                </div>
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" />
                </svg>
            </a>
            <a href="{{ route('waiting-list.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Liste d'attente</p>
                    <p class="truncate text-sm text-gray-500">Gérer la liste d'attente</p>
                </div>
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        <!-- Dernières réservations -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Dernières réservations</h3>
                <div class="mt-4">
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @forelse($latestReservations as $reservation)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900">
                                            {{ $reservation->user->name }}
                                        </p>
                                        <p class="truncate text-sm text-gray-500">
                                            Place n°{{ $reservation->parkingSpot->number }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                            {{ $reservation->status }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <p class="text-sm text-gray-500">{{ $reservation->starts_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="py-4">
                                <p class="text-sm text-gray-500 text-center">Aucune réservation</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection