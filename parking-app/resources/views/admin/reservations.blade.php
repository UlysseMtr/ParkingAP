@extends('layouts.app')

@section('title', 'Gestion des réservations')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Gestion des réservations</h2>

                    <div class="flex space-x-2">
                        <span class="inline-flex rounded-md shadow-sm">
                            <a href="{{ route('admin.reservations.index', ['status' => 'all']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                Toutes
                                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $status === 'all' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $totalCount }}</span>
                            </a>
                        </span>
                        <span class="inline-flex rounded-md shadow-sm">
                            <a href="{{ route('admin.reservations.index', ['status' => 'active']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'active' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                Actives
                                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $status === 'active' ? 'bg-indigo-500 text-white' : 'bg-green-100 text-green-800' }}">{{ $activeCount }}</span>
                            </a>
                        </span>
                        <span class="inline-flex rounded-md shadow-sm">
                            <a href="{{ route('admin.reservations.index', ['status' => 'closed']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'closed' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                Terminées
                                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $status === 'closed' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $closedCount }}</span>
                            </a>
                        </span>
                    </div>
                </div>

                <div class="mb-6">
                    <form action="{{ route('admin.reservations.index') }}" method="GET" class="flex items-center">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <div class="relative flex-grow">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom ou n° de place..." class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @if($search)
                            <a href="{{ route('admin.reservations.index', ['status' => $status]) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            @endif
                        </div>
                        <button type="submit" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Rechercher
                        </button>
                    </form>
                </div>

                @if($search)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Résultats de recherche pour "<strong>{{ $search }}</strong>"
                                <a href="{{ route('admin.reservations.index', ['status' => $status]) }}" class="font-medium underline text-blue-700 hover:text-blue-600">Effacer la recherche</a>
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if($reservations->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucune réservation</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($status === 'active')
                        Il n'y a actuellement aucune réservation active.
                        @elseif($status === 'closed')
                        Il n'y a actuellement aucune réservation terminée.
                        @else
                        Il n'y a actuellement aucune réservation dans le système.
                        @endif
                    </p>
                </div>
                @else
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Utilisateur</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Place</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Début</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Fin</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($reservations as $reservation)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $reservation->user->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    Place n°{{ $reservation->parkingSpot->number ?? 'Supprimée' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $reservation->starts_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $reservation->ends_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $reservation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $reservation->status === 'active' ? 'Active' : 'Terminée' }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($reservation->status === 'active')
                                    <button type="button" onclick="openModal('modal-{{ $reservation->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                        Terminer<span class="sr-only">, réservation {{ $reservation->id }}</span>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reservations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modales de confirmation pour chaque réservation active -->
@foreach($reservations as $reservation)
@if($reservation->status === 'active')
<div id="modal-{{ $reservation->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('modal-{{ $reservation->id }}')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Terminer la réservation
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Êtes-vous sûr de vouloir terminer cette réservation pour l'utilisateur <strong>{{ $reservation->user->name }}</strong> (place n°{{ $reservation->parkingSpot->number ?? 'Supprimée' }}) ? Cette action est irréversible.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                <button type="button" onclick="closeModal('modal-{{ $reservation->id }}')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Annuler
                </button>
                <form method="POST" action="{{ route('reservations.close', $reservation) }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection