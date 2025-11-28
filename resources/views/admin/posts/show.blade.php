<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold gradient-text-purple">
                    {{ __('Post Details') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $post->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.posts.edit', $post) }}" class="admin-quick-action bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <form action="{{ route('admin.posts.publish', $post) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="admin-quick-action {{ $post->status->value === 'published' ? 'bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600' : 'bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600' }} text-sm">
                        @if($post->status->value === 'published')
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Unpublish
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Publish
                        @endif
                    </button>
                </form>
                <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg text-sm transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Posts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card-gradient">
                <div class="p-6 lg:p-8">
                    <!-- Post Header -->
                    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $post->title }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                By {{ $post->author->name }}
                            </div>
                            <span>•</span>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $post->created_at->format('M j, Y') }}
                            </div>
                            <span>•</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $post->status->value === 'published' ? 'bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-800 dark:text-green-300' : 'bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30 text-yellow-800 dark:text-yellow-300' }}">
                                {{ $post->status->label() }}
                            </span>
                            @if($post->published_at)
                                <span>•</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Published: {{ $post->published_at->format('M j, Y') }}
                                </div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 font-mono bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-lg inline-block">
                            <strong>Slug:</strong> {{ $post->slug }}
                        </div>
                    </div>

                    <!-- Categories and Tags -->
                    @if($post->categories->count() > 0 || $post->tags->count() > 0)
                        <div class="mb-8">
                            @if($post->categories->count() > 0)
                                <div class="mb-3">
                                    <strong class="text-gray-700 dark:text-gray-300 block mb-2">Categories:</strong>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($post->categories as $category)
                                            <span class="inline-block bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 text-blue-800 dark:text-blue-300 rounded-full px-3 py-1 text-sm font-medium">{{ $category->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($post->tags->count() > 0)
                                <div>
                                    <strong class="text-gray-700 dark:text-gray-300 block mb-2">Tags:</strong>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($post->tags as $tag)
                                            <span class="inline-block bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 text-purple-800 dark:text-purple-300 rounded-full px-3 py-1 text-sm font-medium">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Excerpt -->
                    @if($post->excerpt)
                        <div class="mb-8 p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg border-l-4 border-purple-500">
                            <p class="text-gray-700 dark:text-gray-300 italic">{{ $post->excerpt }}</p>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="mb-8 prose prose-lg dark:prose-invert max-w-none">
                        <div class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $post->content }}</div>
                    </div>

                    <!-- SEO Meta -->
                    @if($post->meta_title || $post->meta_description)
                        <div class="mb-8 p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg border-l-4 border-indigo-500">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                SEO Information
                            </h3>
                            @if($post->meta_title)
                                <div class="mb-3">
                                    <strong class="text-gray-700 dark:text-gray-300">Meta Title:</strong>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">{{ $post->meta_title }}</span>
                                </div>
                            @endif
                            @if($post->meta_description)
                                <div>
                                    <strong class="text-gray-700 dark:text-gray-300">Meta Description:</strong>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">{{ $post->meta_description }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Comments -->
                    @if($post->comments->count() > 0)
                        <div class="mb-8">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center text-lg">
                                <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Comments ({{ $post->comments->count() }})
                            </h3>
                            <div class="space-y-4">
                                @foreach($post->comments as $comment)
                                    <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg border-l-4 border-pink-500">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold text-xs mr-2">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong class="text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</strong>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="admin-quick-action bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Post
                        </a>
                        <button
                            type="button"
                            onclick="confirmDelete({{ $post->id }}, '{{ addslashes($post->title) }}')"
                            class="admin-quick-action bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Post
                        </button>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="hidden" id="delete-form-{{ $post->id }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(postId, postTitle) {
            if (confirm(`Are you sure you want to delete this post?\n\n"${postTitle}"\n\nThis action cannot be undone.`)) {
                document.getElementById('delete-form-' + postId).submit();
            }
        }
    </script>
    @endpush
</x-admin-layout>
