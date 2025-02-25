@extends('layouts.app')

@section('title', 'Mes notifications')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Mes notifications</h2>
                    @if($notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Tout marquer comme lu
                        </button>
                    </form>
                    @endif
                </div>

                @if($notifications->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Pas de notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">Vous n'avez aucune notification pour le moment.</p>
                </div>
                @else
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($notifications as $notification)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $notification->isUnread() ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center ring-8 ring-white">
                                            @if($notification->type === 'spot_available')
                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                            </svg>
                                            @else
                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            <time datetime="{{ $notification->created_at }}">{{ $notification->created_at->diffForHumans() }}</time>
                                            @if($notification->isUnread())
                                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-500">
                                                    Marquer comme lu
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection