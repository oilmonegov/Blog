<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold gradient-text-purple">
                    {{ __('Create Category') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new category</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card-gradient">
                <div class="p-6 lg:p-8">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-6">
                            <x-input-label for="name" value="Name *" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <x-input-label for="description" value="Description" class="text-gray-700 dark:text-gray-300" />
                            <x-textarea id="description" name="description" class="mt-2 block w-full" rows="5">{{ old('description') }}</x-textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional description for this category</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="admin-quick-action bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
