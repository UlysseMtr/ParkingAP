@extends('layouts.app')

@section('title', 'Liste d\'attente')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Section des statistiques -->
        <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total des personnes en attente -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Personnes en attente</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_waiting'] }}</dd>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">
                        @if($stats['total_waiting'] > 0)
                        <span class="text-yellow-600">
                            <svg class="inline-block h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            Temps d'attente estimé
                        </span>
                        @else
                        <span class="text-green-600">
                            <svg class="inline-block h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Aucune attente
                        </span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Temps d'attente moyen -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Temps d'attente moyen</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    {{ $stats['avg_wait_time'] }}
                    <span class="text-lg">jours</span>
                </dd>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">
                        Basé sur les données historiques
                    </span>
                </div>
            </div>

            <!-- Place la plus demandée -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Place la plus demandée</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    @if($stats['most_requested_spot'])
                    N°{{ $stats['most_requested_spot']['spot_number'] }}
                    @else
                    -
                    @endif
                </dd>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">
                        @if($stats['most_requested_spot'])
                        {{ $stats['most_requested_spot']['count'] }} demandes
                        @else
                        Aucune donnée disponible
                        @endif
                    </span>
                </div>
            </div>

            <!-- Votre position -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Votre position</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    @if($stats['your_position'])
                    {{ $stats['your_position'] }}
                    @else
                    -
                    @endif
                </dd>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">
                        @if($stats['estimated_wait'])
                        Attente estimée: {{ $stats['estimated_wait'] }}
                        @elseif($stats['your_position'])
                        En attente d'une place
                        @else
                        Vous n'êtes pas en liste d'attente
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Graphiques et visualisations -->
        @if(auth()->user()->isAdmin() && $stats['total_waiting'] > 0)
        <div class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Distribution des demandes par place -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Distribution des demandes par place</h3>
                    <div class="mt-4">
                        @foreach($spotDistribution as $item)
                        <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Place N°{{ $item['spot_number'] }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $item['count'] }}</span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, ($item['count'] / $stats['total_waiting']) * 100) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tendance des demandes sur les 7 derniers jours -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Tendance des demandes (7 derniers jours)</h3>
                    <div class="mt-4 h-64 flex items-end space-x-2">
                        @foreach($lastWeekRequests as $index => $count)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-indigo-100 rounded-t-md" style="height: {{ $count > 0 ? (min(100, ($count / max($lastWeekRequests)) * 100)) : 0 }}%">
                                <div class="bg-indigo-600 w-full h-full rounded-t-md"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $lastWeekLabels[$index] }}</div>
                            <div class="text-xs font-medium">{{ $count }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Liste d'attente principale -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Liste d'attente</h2>
                    @if(!auth()->user()->hasActiveReservation() && !auth()->user()->isInWaitingList())
                    <button type="button" onclick="document.getElementById('joinWaitingListModal').classList.remove('hidden')" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Rejoindre la liste d'attente
                    </button>
                    @endif
                </div>

                @if($waitingList->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Liste d'attente vide</h3>
                    <p class="mt-1 text-sm text-gray-500">Il n'y a actuellement personne en attente d'une place.</p>

                    @if(!auth()->user()->hasActiveReservation())
                    <div class="mt-6">
                        <button type="button" onclick="document.getElementById('joinWaitingListModal').classList.remove('hidden')" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Rejoindre la liste d'attente
                        </button>
                    </div>
                    @endif
                </div>
                @else
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Position</th>
                                @if(auth()->user()->isAdmin())
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Utilisateur</th>
                                @endif
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Place demandée</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date de demande</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Attente</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if(auth()->user()->isAdmin())
                            @foreach($waitingList as $entry)
                            <tr class="{{ $entry->user_id === auth()->id() ? 'bg-blue-50' : '' }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium {{ $entry->position <= 3 ? 'text-green-700' : 'text-gray-900' }} sm:pl-6">
                                    @if($entry->position === 1)
                                    <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                        {{ $entry->position }}
                                        <svg class="ml-1 h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    @else
                                    {{ $entry->position }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        @if($entry->user_id === auth()->id())
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 mr-2">
                                            Vous
                                        </span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $entry->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                Place n°{{ $entry->parkingSpot->number ?? 'Indéterminée' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->requested_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->requested_at->diffForHumans() }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($entry->user_id === auth()->id())
                                    <button type="button" onclick="openCancelModal()" class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-sm font-medium text-red-700 hover:bg-red-100">
                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Quitter la liste
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            @foreach($waitingList as $entry)
                            @if($entry->user_id === auth()->id())
                            <tr class="bg-blue-50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium {{ $entry->position <= 3 ? 'text-green-700' : 'text-gray-900' }} sm:pl-6">
                                    @if($entry->position === 1)
                                    <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                        {{ $entry->position }}
                                        <svg class="ml-1 h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    @else
                                    {{ $entry->position }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        @if($entry->user_id === auth()->id())
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 mr-2">
                                            Vous
                                        </span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $entry->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                Place n°{{ $entry->parkingSpot->number ?? 'Indéterminée' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->requested_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->requested_at->diffForHumans() }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <button type="button" onclick="openCancelModal()" class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-sm font-medium text-red-700 hover:bg-red-100">
                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Quitter la liste
                                    </button>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $entry->position }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $entry->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                Place n°{{ $entry->parkingSpot->number ?? 'Indéterminée' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="text-gray-400">Confidentiel</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="text-gray-400">Confidentiel</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <!-- Aucune action disponible -->
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @endif

                @if(auth()->user()->isInWaitingList())
                <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Vous serez automatiquement notifié lorsqu'une place se libérera. Vous aurez alors 24 heures pour utiliser votre réservation.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if(!auth()->user()->isAdmin() && $stats['total_waiting'] > 0 && !auth()->user()->isInWaitingList())
                <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Pour des raisons de confidentialité, vous ne pouvez voir que votre propre position dans la liste d'attente. Il y a actuellement {{ $stats['total_waiting'] }} personnes en attente.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour rejoindre la liste d'attente -->
<div id="joinWaitingListModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div>
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                        Rejoindre la liste d'attente
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Sélectionnez la place de parking pour laquelle vous souhaitez être en liste d'attente.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('waiting-list.join') }}" class="mt-5 sm:mt-6">
                @csrf
                <div>
                    <label for="parking_spot_id" class="block text-sm font-medium leading-6 text-gray-900">
                        Place de parking
                    </label>
                    <select id="parking_spot_id" name="parking_spot_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Sélectionnez une place</option>
                        @foreach($parkingSpots as $spot)
                        @if(!$spot->isAvailable())
                        <option value="{{ $spot->id }}">Place n°{{ $spot->number }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                        Confirmer
                    </button>
                    <button type="button" onclick="document.getElementById('joinWaitingListModal').classList.add('hidden')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour quitter la liste d'attente -->
<div id="cancelWaitingListModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div>
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                        Quitter la liste d'attente
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Êtes-vous sûr de vouloir quitter la liste d'attente ? Vous perdrez votre position actuelle.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                <form method="POST" action="{{ route('waiting-list.cancel') }}" class="sm:col-start-2">
                    @csrf
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                        Confirmer
                    </button>
                </form>
                <button type="button" onclick="closeCancelModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openCancelModal() {
        document.getElementById('cancelWaitingListModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelWaitingListModal').classList.add('hidden');
    }
</script>
@endsection