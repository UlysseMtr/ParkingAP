@extends('layouts.app')

@section('title', 'Tableau de bord administrateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places totales</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places disponibles</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_spots'] - $stats['occupied_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places occupées</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['occupied_spots'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Utilisateurs</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_users'] }}</dd>
            </div>
        </div>
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Occupation des places</h3>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Occupées</span>
                                <span class="text-sm font-medium text-gray-700">{{ $stats['occupied_spots'] }} places</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $percentages['occupation'] }}%;"></div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Disponibles</span>
                                <span class="text-sm font-medium text-gray-700">{{ $stats['active_spots'] - $stats['occupied_spots'] }} places</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full" style="width: {{ $percentages['disponibilite'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4 text-center text-sm">
                    <div class="p-2 rounded-lg bg-indigo-50">
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 rounded-full bg-indigo-600 mr-2"></div>
                            <span class="font-medium">Occupées</span>
                        </div>
                        <div class="font-semibold text-lg mt-1">{{ $percentages['occupation'] }}%</div>
                    </div>
                    <div class="p-2 rounded-lg bg-green-50">
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                            <span class="font-medium">Disponibles</span>
                        </div>
                        <div class="font-semibold text-lg mt-1">{{ $percentages['disponibilite'] }}%</div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Statut des réservations</h3>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Actives</span>
                                <span class="text-sm font-medium text-gray-700">{{ $reservationStatuses['active'] }}</span>
                            </div>
                            @php
                            $totalReservations = $reservationStatuses['active'] + $reservationStatuses['closed'];
                            $activePercentage = $totalReservations > 0 ? ($reservationStatuses['active'] / $totalReservations) * 100 : 0;
                            $closedPercentage = $totalReservations > 0 ? ($reservationStatuses['closed'] / $totalReservations) * 100 : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full" style="width: {{ $activePercentage }}%;"></div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Terminées</span>
                                <span class="text-sm font-medium text-gray-700">{{ $reservationStatuses['closed'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-gray-500 h-4 rounded-full" style="width: {{ $closedPercentage }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4 text-center text-sm">
                    <div class="p-2 rounded-lg bg-green-50">
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                            <span class="font-medium">Actives</span>
                        </div>
                        <div class="font-semibold text-lg mt-1">{{ $reservationStatuses['active'] }}</div>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50">
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 rounded-full bg-gray-500 mr-2"></div>
                            <span class="font-medium">Terminées</span>
                        </div>
                        <div class="font-semibold text-lg mt-1">{{ $reservationStatuses['closed'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-8 overflow-hidden rounded-lg bg-white px-6 py-6 shadow sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Évolution des réservations (7 derniers jours)</h3>
            <div class="h-64 flex items-end justify-between px-4">
                @foreach($lastWeekReservations as $index => $count)
                @php
                $maxCount = max($lastWeekReservations) > 0 ? max($lastWeekReservations) : 1;
                $height = ($count / $maxCount) * 100;
                @endphp
                <div class="flex flex-col items-center">
                    <div class="w-16 bg-indigo-600 rounded-t-lg shadow-md" style="height: {{ $height }}px; min-height: {{ $count > 0 ? '20px' : '4px' }};"></div>
                    <div class="mt-2 text-sm font-medium">{{ $lastWeekLabels[$index] }}</div>
                    <div class="text-sm font-semibold text-indigo-700">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('admin.users.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 hover:bg-gray-50 transition duration-150">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Gestion des utilisateurs</p>
                    <p class="truncate text-sm text-gray-500">Gérer les comptes utilisateurs</p>
                </div>
                <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
            </a>
            <a href="{{ route('parking-spots.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 hover:bg-gray-50 transition duration-150">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Places de parking</p>
                    <p class="truncate text-sm text-gray-500">Gérer les places de parking</p>
                </div>
                <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 000 2h.01a1 1 0 100-2H10a1 1 0 00-1-1z" />
                </svg>
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 hover:bg-gray-50 transition duration-150">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">Gestion des réservations</p>
                    <p class="truncate text-sm text-gray-500">Gérer les réservations actives</p>
                </div>
                <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-6 py-6 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Dernières réservations</h3>
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
                                            Place n°{{ $reservation->parkingSpot->number ?? 'Supprimée' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $reservation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $reservation->status === 'active' ? 'Active' : 'Terminée' }}
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
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Taux d'occupation</h3>

                @php
                $occupationRate = $stats['total_spots'] > 0 ? round(($stats['occupied_spots'] / $stats['total_spots']) * 100) : 0;
                $colorClass = $occupationRate < 50 ? 'text-green-600' : ($occupationRate < 80 ? 'text-yellow-600' : 'text-red-600' );
                    @endphp

                    <div class="flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-5xl font-bold {{ $colorClass }}">{{ $occupationRate }}%</div>
                        <div class="mt-2 text-sm text-gray-500">des places sont actuellement occupées</div>

                        <div class="mt-6 w-full bg-gray-200 rounded-full h-4">
                            <div class="h-4 rounded-full {{ $occupationRate < 50 ? 'bg-green-500' : ($occupationRate < 80 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $occupationRate }}%;"></div>
                        </div>

                        <div class="mt-4 text-sm">
                            @if($occupationRate < 50)
                                <span class="text-green-600 font-medium">Faible affluence</span>
                                @elseif($occupationRate < 80)
                                    <span class="text-yellow-600 font-medium">Affluence modérée</span>
                                    @else
                                    <span class="text-red-600 font-medium">Forte affluence</span>
                                    @endif
                        </div>
                    </div>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Alertes et notifications</h3>

            <div class="space-y-4">
                @if($stats['occupied_spots'] >= $stats['active_spots'])
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Parking complet</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Toutes les places disponibles sont actuellement occupées.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($stats['active_spots'] < $stats['total_spots'])
                    <div class="rounded-md bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Places inactives</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>{{ $stats['total_spots'] - $stats['active_spots'] }} place(s) sont actuellement désactivées.</p>
                            </div>
                            <div class="mt-4">
                                <div class="-mx-2 -my-1.5 flex">
                                    <a href="{{ route('parking-spots.index') }}" class="rounded-md bg-blue-50 px-2 py-1.5 text-sm font-medium text-blue-800 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-blue-50">
                                        Gérer les places
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
</div>
@endsection