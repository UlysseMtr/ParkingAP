@extends('layouts.app')

@section('title', 'Mes réservations')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Mes réservations</h2>

                @if($reservations->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucune réservation</h3>
                    <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore effectué de réservation.</p>
                    <div class="mt-6">
                        <a href="{{ route('parking-spots.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Réserver une place
                        </a>
                    </div>
                </div>
                @else
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Place</th>
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
                                    Place n°{{ $reservation->parkingSpot->number }}
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
                                    <form method="POST" action="{{ route('reservations.close', $reservation) }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir terminer cette réservation ?');">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                            Terminer<span class="sr-only">, réservation {{ $reservation->id }}</span>
                                        </button>
                                    </form>
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
@endsection