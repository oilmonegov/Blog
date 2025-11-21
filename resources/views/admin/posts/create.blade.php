<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Post') }}
            </h2>
            <a href="{{ route('admin.posts.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Posts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.posts.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" value="Title *" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Slug (optional) -->
                        <div class="mb-4">
                            <x-input-label for="slug" value="Slug (optional - auto-generated if empty)" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug')" />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <x-input-label for="content" value="Content *" />
                            <x-textarea id="content" name="content" class="mt-1 block w-full" rows="10" required>{{ old('content') }}</x-textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-4">
                            <x-input-label for="excerpt" value="Excerpt (optional - auto-generated if empty)" />
                            <x-textarea id="excerpt" name="excerpt" class="mt-1 block w-full" rows="3">{{ old('excerpt') }}</x-textarea>
                            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <x-input-label for="categories" value="Categories" />
                            <select id="categories" name="categories[]" multiple class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" size="5">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Windows) or Cmd (Mac) to select multiple categories</p>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <x-input-label for="tags" value="Tags (comma-separated)" />
                            <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full" :value="old('tags')" placeholder="tag1, tag2, tag3" />
                            <p class="mt-1 text-sm text-gray-500">Separate tags with commas. Tags will be created automatically if they don't exist.</p>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" value="Status *" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', 'draft') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Meta Title -->
                        <div class="mb-4">
                            <x-input-label for="meta_title" value="Meta Title (SEO)" />
                            <x-text-input id="meta_title" name="meta_title" type="text" class="mt-1 block w-full" :value="old('meta_title')" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-4">
                            <x-input-label for="meta_description" value="Meta Description (SEO)" />
                            <x-textarea id="meta_description" name="meta_description" class="mt-1 block w-full" rows="3">{{ old('meta_description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.posts.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-primary-button>
                                Create Post
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

