<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold gradient-text-purple">
                    {{ __('Edit Post') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update post: {{ $post->title }}</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Posts
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card-gradient">
                <div class="p-6 lg:p-8">
                    <form method="POST" action="{{ route('admin.posts.update', $post) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Title -->
                        <div class="mb-6">
                            <x-input-label for="title" value="Title *" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block w-full" :value="old('title', $post->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Slug -->
                        <div class="mb-6">
                            <x-input-label for="slug" value="Slug" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-2 block w-full font-mono" :value="old('slug', $post->slug)" />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <x-input-label for="content" value="Content *" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <x-textarea id="content" name="content" class="mt-2 block w-full" rows="12" required>{{ old('content', $post->content) }}</x-textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-6">
                            <x-input-label for="excerpt" value="Excerpt" class="text-gray-700 dark:text-gray-300" />
                            <x-textarea id="excerpt" name="excerpt" class="mt-2 block w-full" rows="3">{{ old('excerpt', $post->excerpt) }}</x-textarea>
                            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                        </div>

                        <!-- Categories -->
                        <div class="mb-6">
                            <x-input-label for="categories" value="Categories" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <select id="categories" name="categories[]" multiple class="mt-2 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" size="5">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hold Ctrl (Windows) or Cmd (Mac) to select multiple categories</p>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <!-- Tags -->
                        <div class="mb-6">
                            <x-input-label for="tags" value="Tags (comma-separated)" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <x-text-input id="tags" name="tags" type="text" class="mt-2 block w-full" :value="old('tags', $post->tags->pluck('name')->join(', '))" placeholder="tag1, tag2, tag3" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Separate tags with commas. Tags will be created automatically if they don't exist.</p>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <x-input-label for="status" value="Status *" class="text-gray-700 dark:text-gray-300 font-semibold" />
                            <select id="status" name="status" class="mt-2 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $post->status->value) === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Meta Title -->
                        <div class="mb-6">
                            <x-input-label for="meta_title" value="Meta Title (SEO)" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input id="meta_title" name="meta_title" type="text" class="mt-2 block w-full" :value="old('meta_title', $post->meta_title)" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-6">
                            <x-input-label for="meta_description" value="Meta Description (SEO)" class="text-gray-700 dark:text-gray-300" />
                            <x-textarea id="meta_description" name="meta_description" class="mt-2 block w-full" rows="3">{{ old('meta_description', $post->meta_description) }}</x-textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="admin-quick-action bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
