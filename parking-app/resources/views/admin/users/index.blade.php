@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Utilisateurs</h2>
                    <button type="button" onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Ajouter un utilisateur
                    </button>
                </div>

                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Nom</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Rôle</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Réservations</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ $user->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span class="inline-flex items-center rounded-md {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700 ring-purple-600/20' : 'bg-gray-50 text-gray-700 ring-gray-600/20' }} px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span class="inline-flex items-center rounded-md {{ $user->is_active ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }} px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->reservations_count }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                            <button type="button" onclick="document.getElementById('editUser{{ $user->id }}Modal').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-900">
                                                Modifier
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de modification -->
                                    <div id="editUser{{ $user->id }}Modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
                                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                            <div class="flex justify-between items-center mb-4">
                                                <h3 class="text-lg font-medium text-gray-900">Modifier l'utilisateur</h3>
                                                <button type="button" onclick="document.getElementById('editUser{{ $user->id }}Modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                                    <span class="sr-only">Fermer</span>
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-4">
                                                    <label for="name{{ $user->id }}" class="block text-sm font-medium text-gray-700">Nom</label>
                                                    <input type="text" name="name" id="name{{ $user->id }}" value="{{ $user->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="mb-4">
                                                    <label for="email{{ $user->id }}" class="block text-sm font-medium text-gray-700">Email</label>
                                                    <input type="email" name="email" id="email{{ $user->id }}" value="{{ $user->email }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="mb-4">
                                                    <label for="role{{ $user->id }}" class="block text-sm font-medium text-gray-700">Rôle</label>
                                                    <select name="role" id="role{{ $user->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="is_active{{ $user->id }}" class="block text-sm font-medium text-gray-700">Statut</label>
                                                    <select name="is_active" id="is_active{{ $user->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Actif</option>
                                                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactif</option>
                                                    </select>
                                                </div>
                                                <div class="mt-6 flex justify-end space-x-3">
                                                    <button type="button" onclick="document.getElementById('editUser{{ $user->id }}Modal').classList.add('hidden')" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                        Enregistrer
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="mt-4 pt-4 border-t">
                                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                                                        Réinitialiser le mot de passe
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création -->
<div id="createUserModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Nouvel utilisateur</h3>
            <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                <select name="role" id="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
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