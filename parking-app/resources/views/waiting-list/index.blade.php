@extends('layouts.app')

@section('title', 'Liste d\'attente')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Liste d'attente vide</h3>
                    <p class="mt-1 text-sm text-gray-500">Il n'y a actuellement personne en attente d'une place.</p>
                </div>
                @else
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Position</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Utilisateur</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Place demandée</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date de demande</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($waitingList as $entry)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $entry->position }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->user->name }}
                                    @if($entry->user_id === auth()->id())
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                        Vous
                                    </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    Place n°{{ $entry->parkingSpot->number }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $entry->requested_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($entry->user_id === auth()->id())
                                    <form method="POST" action="{{ route('waiting-list.cancel') }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter la liste d\'attente ?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Quitter la liste
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if(auth()->user()->isInWaitingList())
                <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
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
            </div>
        </div>
    </div>
</div>

<!-- Modal pour rejoindre la liste d'attente -->
<div id="joinWaitingListModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div>
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
@endsection