<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Post Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('author.posts.edit', $post) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <form action="{{ route('author.posts.publish', $post) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        {{ $post->status->value === 'published' ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('author.posts.index') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Posts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Post Header -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                            <span>By {{ $post->author->name }}</span>
                            <span>•</span>
                            <span>{{ $post->created_at->format('M j, Y') }}</span>
                            <span>•</span>
                            <span class="px-2 py-1 rounded-full {{ $post->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $post->status->label() }}
                            </span>
                            @if($post->published_at)
                                <span>•</span>
                                <span>Published: {{ $post->published_at->format('M j, Y') }}</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Slug:</strong> {{ $post->slug }}
                        </div>
                    </div>

                    <!-- Categories and Tags -->
                    @if($post->categories->count() > 0 || $post->tags->count() > 0)
                        <div class="mb-6">
                            @if($post->categories->count() > 0)
                                <div class="mb-2">
                                    <strong class="text-gray-700">Categories:</strong>
                                    @foreach($post->categories as $category)
                                        <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm mr-2">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if($post->tags->count() > 0)
                                <div>
                                    <strong class="text-gray-700">Tags:</strong>
                                    @foreach($post->tags as $tag)
                                        <span class="inline-block bg-blue-100 rounded-full px-3 py-1 text-sm mr-2">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Excerpt -->
                    @if($post->excerpt)
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700 italic">{{ $post->excerpt }}</p>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="mb-6 prose max-w-none">
                        <div class="text-gray-900 whitespace-pre-wrap">{{ $post->content }}</div>
                    </div>

                    <!-- SEO Meta -->
                    @if($post->meta_title || $post->meta_description)
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">SEO Information</h3>
                            @if($post->meta_title)
                                <div class="mb-2">
                                    <strong class="text-gray-700">Meta Title:</strong>
                                    <span class="text-gray-600">{{ $post->meta_title }}</span>
                                </div>
                            @endif
                            @if($post->meta_description)
                                <div>
                                    <strong class="text-gray-700">Meta Description:</strong>
                                    <span class="text-gray-600">{{ $post->meta_description }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Comments -->
                    @if($post->comments->count() > 0)
                        <div class="mt-8">
                            <h3 class="font-semibold text-gray-900 mb-4">Comments ({{ $post->comments->count() }})</h3>
                            <div class="space-y-4">
                                @foreach($post->comments as $comment)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <strong class="text-gray-900">{{ $comment->user->name }}</strong>
                                                <span class="text-sm text-gray-500 ml-2">{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                                            </div>
                                        </div>
                                        <p class="text-gray-700">{{ $comment->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-8 flex gap-2">
                        <a href="{{ route('author.posts.edit', $post) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Post
                        </a>
                        <form action="{{ route('author.posts.destroy', $post) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button
                                type="button"
                                onclick="confirmDelete({{ $post->id }}, '{{ addslashes($post->title) }}')"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Delete Post
                            </button>
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
                const form = document.querySelector(`form[action*="/posts/${postId}"][method="POST"]`);
                if (form) {
                    form.submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

