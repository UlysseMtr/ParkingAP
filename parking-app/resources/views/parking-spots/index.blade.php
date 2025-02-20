@extends('layouts.app')

@section('title', 'Places de parking')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Places de parking</h2>
                    @if(auth()->user()->isAdmin())
                    <button type="button" onclick="document.getElementById('createSpotModal').classList.remove('hidden')" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
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
                    <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400">
                        <div class="min-w-0 flex-1">
                            <div class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900">Place n°{{ $spot->number }}</p>
                                <p class="truncate text-sm text-gray-500">{{ $spot->description }}</p>
                                @if($spot->currentReservation)
                                <p class="mt-2 text-xs text-red-600">
                                    Occupée par {{ $spot->currentReservation->user->name }}
                                </p>
                                @else
                                <p class="mt-2 text-xs text-green-600">Disponible</p>
                                @endif
                            </div>
                        </div>
                        @if(!$spot->currentReservation && !auth()->user()->hasActiveReservation() && !auth()->user()->isInWaitingList())
                        <form method="POST" action="{{ route('reservations.store') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Réserver
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer une place -->
<div id="createSpotModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Nouvelle place de parking</h3>
            <button type="button" onclick="document.getElementById('createSpotModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ url('/admin/parking-spots') }}" onsubmit="event.preventDefault(); if(confirm('Voulez-vous créer cette place de parking ?')) this.submit();">
            @csrf
            <div class="mb-4">
                <label for="number" class="block text-sm font-medium text-gray-700">Numéro de place</label>
                <input type="text" name="number" id="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('createSpotModal').classList.add('hidden')" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection