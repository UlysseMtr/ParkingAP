<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title', 'Gestion du Parking')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full">
    @auth
    <nav class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <a href="{{ route('parking-spots.index') }}" class="text-xl font-bold text-indigo-600">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('parking-spots.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 {{ request()->routeIs('parking-spots.*') ? 'border-b-2 border-indigo-500' : '' }}">
                            Places
                        </a>
                        <a href="{{ route('reservations.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 {{ request()->routeIs('reservations.*') ? 'border-b-2 border-indigo-500' : '' }}">
                            Mes Réservations
                        </a>
                        <a href="{{ route('waiting-list.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 {{ request()->routeIs('waiting-list.*') ? 'border-b-2 border-indigo-500' : '' }}">
                            Liste d'attente
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 {{ request()->routeIs('admin.*') ? 'border-b-2 border-indigo-500' : '' }}">
                            Administration
                        </a>
                        @endif
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="relative ml-3">
                        <div class="flex items-center">
                            <div class="relative">
                                <button type="button" onclick="document.getElementById('profileDropdown').classList.toggle('hidden')" class="flex items-center gap-x-1 text-gray-700 hover:text-gray-900">
                                    <span class="text-gray-900">{{ auth()->user()->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="profileDropdown" class="hidden absolute right-0 z-10 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="min-h-screen">
        @if(session('status'))
        <div id="status-message" class="bg-green-50 border-l-4 border-green-400 p-4 fixed top-4 right-4 z-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var element = document.getElementById('status-message');
                if (element) {
                    element.style.transition = 'opacity 0.5s ease-in-out';
                    element.style.opacity = '0';
                    setTimeout(function() {
                        element.remove();
                    }, 500);
                }
            }, 3000);
        </script>
        @endif

        @if(session('error'))
        <div id="error-message" class="bg-red-50 border-l-4 border-red-400 p-4 fixed top-4 right-4 z-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var element = document.getElementById('error-message');
                if (element) {
                    element.style.transition = 'opacity 0.5s ease-in-out';
                    element.style.opacity = '0';
                    setTimeout(function() {
                        element.remove();
                    }, 500);
                }
            }, 3000);
        </script>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
            <div class="mt-8 md:order-1 md:mt-0">
                <p class="text-center text-xs leading-5 text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
</body>

</html>