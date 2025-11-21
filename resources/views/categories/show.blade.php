<x-public-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-gray-600">{{ $category->description }}</p>
            @endif
        </div>

        @if($posts->count() > 0)
            <div class="space-y-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->format('F j, Y') }}
                                </time>
                                <span>•</span>
                                <span>By {{ $post->author->name }}</span>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-900 mb-3">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            @if($post->excerpt)
                                <p class="text-gray-700 mb-4 line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                @if($post->categories->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($post->categories as $cat)
                                            <a href="{{ route('categories.show', $cat->slug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors {{ $cat->id === $category->id ? 'ring-2 ring-indigo-500' : '' }}">
                                                {{ $cat->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if($post->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($post->tags as $tag)
                                            <a href="{{ route('tags.show', $tag->slug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                Read more
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-600 text-lg">No posts in this category yet. Check back soon!</p>
            </div>
        @endif
    </div>
</x-public-layout>

