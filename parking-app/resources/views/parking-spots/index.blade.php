@extends('layouts.app')

@section('title', 'Places de parking')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistiques des places -->
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @php
            $totalSpots = $parkingSpots->count();
            $activeSpots = $parkingSpots->where('is_active', true)->count();
            $occupiedSpots = $parkingSpots->filter(function($spot) { return $spot->currentReservation; })->count();
            $availableSpots = $activeSpots - $occupiedSpots;
            $inactiveSpots = $totalSpots - $activeSpots;

            $occupancyRate = $activeSpots > 0 ? round(($occupiedSpots / $activeSpots) * 100) : 0;
            @endphp

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places totales</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalSpots }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places disponibles</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $availableSpots }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places occupées</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $occupiedSpots }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Places inactives</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $inactiveSpots }}</dd>
            </div>
        </div>

        <!-- Visualisation de l'occupation -->
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Taux d'occupation</h3>
                <div class="flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-5xl font-bold {{ $occupancyRate < 50 ? 'text-green-600' : ($occupancyRate < 80 ? 'text-yellow-600' : 'text-red-600') }}">{{ $occupancyRate }}%</div>
                        <div class="mt-2 text-sm text-gray-500">des places actives sont occupées</div>

                        <div class="mt-6 w-full bg-gray-200 rounded-full h-4">
                            <div class="h-4 rounded-full {{ $occupancyRate < 50 ? 'bg-green-500' : ($occupancyRate < 80 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $occupancyRate }}%;"></div>
                        </div>

                        <div class="mt-4 text-sm">
                            @if($occupancyRate < 50)
                                <span class="text-green-600 font-medium">Faible affluence</span>
                                @elseif($occupancyRate < 80)
                                    <span class="text-yellow-600 font-medium">Affluence modérée</span>
                                    @else
                                    <span class="text-red-600 font-medium">Forte affluence</span>
                                    @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-6 py-6 shadow">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Répartition des places</h3>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Occupées</span>
                                <span class="text-sm font-medium text-gray-700">{{ $occupiedSpots }} places</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $totalSpots > 0 ? ($occupiedSpots / $totalSpots) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Disponibles</span>
                                <span class="text-sm font-medium text-gray-700">{{ $availableSpots }} places</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full" style="width: {{ $totalSpots > 0 ? ($availableSpots / $totalSpots) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Inactives</span>
                                <span class="text-sm font-medium text-gray-700">{{ $inactiveSpots }} places</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-gray-500 h-4 rounded-full" style="width: {{ $totalSpots > 0 ? ($inactiveSpots / $totalSpots) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des places -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Liste des places</h2>
                    @if(auth()->user()->isAdmin())
                    <button type="button" onclick="openCreateSpotModal()" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 flex items-center">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter une place
                    </button>
                    @endif
                </div>

                @if($parkingSpots->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucune place</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune place de parking n'a encore été créée.</p>
                </div>
                @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($parkingSpots as $spot)
                    <div class="relative flex items-center space-x-3 rounded-lg border {{ $spot->is_active ? ($spot->currentReservation ? 'border-indigo-300 bg-indigo-50' : 'border-green-300 bg-green-50') : 'border-gray-300 bg-gray-50' }} px-6 py-5 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $spot->is_active ? ($spot->currentReservation ? 'bg-indigo-100 text-indigo-600' : 'bg-green-100 text-green-600') : 'bg-gray-100 text-gray-600' }}">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="focus:outline-none">
                                <p class="text-sm font-medium text-gray-900">Place n°{{ $spot->number }}</p>
                                <p class="truncate text-sm text-gray-500">{{ $spot->description }}</p>
                                @if(!$spot->is_active)
                                <p class="mt-2 text-xs text-gray-600 bg-gray-200 inline-block px-2 py-1 rounded-full">Inactive</p>
                                @elseif($spot->currentReservation)
                                <p class="mt-2 text-xs text-indigo-600 bg-indigo-100 inline-block px-2 py-1 rounded-full">
                                    @if(auth()->user()->isAdmin())
                                    Occupée par {{ $spot->currentReservation->user->name }}
                                    @else
                                    Occupée
                                    @endif
                                </p>
                                @else
                                <p class="mt-2 text-xs text-green-600 bg-green-100 inline-block px-2 py-1 rounded-full">Disponible</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            @if($spot->is_active && !$spot->currentReservation && !auth()->user()->hasActiveReservation() && !auth()->user()->isInWaitingList())
                            <button type="button" onclick="openReservationModal('{{ $spot->id }}', '{{ $spot->number }}')" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Réserver
                            </button>
                            @elseif(auth()->user()->isAdmin())
                            <div class="flex space-x-2">
                                <button type="button" onclick="openEditSpotModal('{{ $spot->id }}', '{{ $spot->number }}', '{{ $spot->description }}')" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Alertes et informations -->
        @if(auth()->user()->isAdmin())
        <div class="mb-8 overflow-hidden rounded-lg bg-white px-6 py-6 shadow">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Alertes et informations</h3>

            <div class="space-y-4">
                @if($inactiveSpots > 0)
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
                                <p>{{ $inactiveSpots }} place(s) sont actuellement désactivées.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($occupancyRate >= 80)
                <div class="rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Occupation élevée</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Le taux d'occupation des places actives est de {{ $occupancyRate }}%.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($availableSpots == 0 && $activeSpots > 0)
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
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal pour créer une place -->
<div id="createSpotModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-0 border w-full max-w-md shadow-xl rounded-xl bg-white transform transition-all">
        <div class="bg-indigo-600 rounded-t-xl px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Nouvelle place de parking
                </h3>
                <button type="button" onclick="document.getElementById('createSpotModal').classList.add('hidden')" class="text-white hover:text-gray-200 transition-colors">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ url('/admin/parking-spots') }}" id="createSpotForm" class="p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 mb-1">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Numéro de place
                        </div>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="number" id="number" required
                            class="block w-full rounded-md border-gray-300 pl-3 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">N°</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Description
                        </div>
                    </label>
                    <div class="mt-1">
                        <textarea name="description" id="description" rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Ajoutez des détails sur cette place de parking..."></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Décrivez l'emplacement ou les caractéristiques spécifiques de cette place.</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button"
                    onclick="document.getElementById('createSpotModal').classList.add('hidden')"
                    class="flex items-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Annuler
                </button>
                <button type="button"
                    onclick="openConfirmCreateModal()"
                    class="flex items-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmation pour la création d'une place de parking -->
<div id="confirmCreateSpotModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
            <div class="bg-indigo-50 px-6 py-6">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 mb-4">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold leading-6 text-gray-900">
                        Confirmer la création
                    </h3>
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">
                            Voulez-vous créer cette place de parking ? Cette action ajoutera une nouvelle place disponible dans le système.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <div class="flex flex-col sm:flex-row-reverse gap-3">
                    <button type="button"
                        onclick="submitCreateSpotForm()"
                        class="flex-1 justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors flex items-center">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Confirmer
                    </button>
                    <button type="button"
                        onclick="closeConfirmCreateModal()"
                        class="flex-1 justify-center rounded-md bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de réservation -->
<div id="reservationModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Réserver une place</h3>
            <button type="button" onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('reservations.store') }}" id="reservationForm">
            @csrf
            <input type="hidden" name="parking_spot_id" id="parking_spot_id">
            <div class="mb-4">
                <p class="text-sm text-gray-700">Vous allez réserver la place n°<span id="spot_number"></span></p>
            </div>
            <div class="mb-4">
                <label for="duration" class="block text-sm font-medium text-gray-700">Durée de réservation</label>
                <select name="duration" id="duration" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="24">24 heures</option>
                    <option value="48">48 heures</option>
                    <option value="72">72 heures</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeReservationModal()" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Confirmer la réservation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour modifier une place -->
<div id="editSpotModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-0 border w-full max-w-md shadow-xl rounded-xl bg-white transform transition-all">
        <div class="bg-indigo-600 rounded-t-xl px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier la place de parking
                </h3>
                <button type="button" onclick="document.getElementById('editSpotModal').classList.add('hidden')" class="text-white hover:text-gray-200 transition-colors">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" id="editSpotForm" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="edit_number" class="block text-sm font-medium text-gray-700 mb-1">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Numéro de place
                        </div>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="number" id="edit_number" required
                            class="block w-full rounded-md border-gray-300 pl-3 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">N°</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Description
                        </div>
                    </label>
                    <div class="mt-1">
                        <textarea name="description" id="edit_description" rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Ajoutez des détails sur cette place de parking..."></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Décrivez l'emplacement ou les caractéristiques spécifiques de cette place.</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button"
                    onclick="document.getElementById('editSpotModal').classList.add('hidden')"
                    class="flex items-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Annuler
                </button>
                <button type="button"
                    onclick="openDeleteFromEdit()"
                    class="flex items-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer
                </button>
                <button type="submit"
                    class="flex items-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour supprimer une place -->
<div id="deleteSpotModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
            <div class="bg-red-50 px-6 py-6">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold leading-6 text-gray-900">
                        Confirmer la suppression
                    </h3>
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">
                            Voulez-vous vraiment supprimer la place n°<span id="delete_spot_number" class="font-medium"></span> ? Cette action est irréversible.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white px-6 py-6">
                <form id="deleteSpotForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit"
                            class="flex-1 justify-center rounded-md bg-red-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition-colors flex items-center">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer
                        </button>
                        <button type="button"
                            onclick="closeDeleteSpotModal()"
                            class="flex-1 justify-center rounded-md bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors flex items-center">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openReservationModal(spotId, spotNumber) {
        document.getElementById('parking_spot_id').value = spotId;
        document.getElementById('spot_number').textContent = spotNumber;
        document.getElementById('reservationModal').classList.remove('hidden');
    }

    function closeReservationModal() {
        document.getElementById('reservationModal').classList.add('hidden');
    }

    function openConfirmCreateModal() {
        document.getElementById('confirmCreateSpotModal').classList.remove('hidden');
    }

    function closeConfirmCreateModal() {
        document.getElementById('confirmCreateSpotModal').classList.add('hidden');
    }

    function submitCreateSpotForm() {
        document.getElementById('createSpotForm').submit();
    }

    function openEditSpotModal(id, number, description) {
        const form = document.getElementById('editSpotForm');
        form.action = `/admin/parking-spots/${id}`;

        document.getElementById('edit_number').value = number;
        document.getElementById('edit_description').value = description || '';

        document.getElementById('editSpotModal').classList.remove('hidden');

        // Focus sur le champ numéro après ouverture du modal
        setTimeout(() => {
            document.getElementById('edit_number').focus();
        }, 100);
    }

    // Fonction pour ouvrir le modal de création avec focus
    function openCreateSpotModal() {
        document.getElementById('createSpotModal').classList.remove('hidden');

        // Réinitialiser le formulaire
        document.getElementById('createSpotForm').reset();

        // Focus sur le champ numéro après ouverture du modal
        setTimeout(() => {
            document.getElementById('number').focus();
        }, 100);
    }

    function openDeleteSpotModal(id, number) {
        const form = document.getElementById('deleteSpotForm');
        form.action = `/admin/parking-spots/${id}`;

        document.getElementById('delete_spot_number').textContent = number;
        document.getElementById('deleteSpotModal').classList.remove('hidden');
    }

    function closeDeleteSpotModal() {
        document.getElementById('deleteSpotModal').classList.add('hidden');
    }

    function openDeleteFromEdit() {
        // Fermer le modal d'édition
        document.getElementById('editSpotModal').classList.add('hidden');

        // Récupérer l'ID et le numéro de la place depuis le formulaire d'édition
        const editForm = document.getElementById('editSpotForm');
        const parkingSpotId = editForm.getAttribute('action').split('/').pop();
        const parkingSpotNumber = document.getElementById('edit_number').value;

        // Ouvrir le modal de suppression avec les bonnes informations
        openDeleteSpotModal(parkingSpotId, parkingSpotNumber);
    }
</script>
@endsection