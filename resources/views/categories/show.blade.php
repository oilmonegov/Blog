<x-public-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 text-white py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-lg md:text-xl text-indigo-100 max-w-2xl mx-auto">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($posts->count() > 0)
            <!-- Responsive Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($posts as $post)
                    <article class="card-gradient overflow-hidden group">
                        <div class="p-6">
                            <!-- Author and Meta Info -->
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="avatar-gradient">
                                    <div>
                                        <span class="text-purple-600 dark:text-purple-400">
                                            {{ strtoupper(substr($post->author->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $post->author->name }}
                                    </p>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <time datetime="{{ $post->published_at->toIso8601String() }}">
                                            {{ $post->published_at->format('M j, Y') }}
                                        </time>
                                        <span>•</span>
                                        <span>{{ $post->reading_time }} min read</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3 group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-pink-500 group-hover:bg-clip-text group-hover:text-transparent transition-all duration-300">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <!-- Excerpt -->
                            @if($post->excerpt)
                                <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            @endif

                            <!-- Categories and Tags -->
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                @if($post->categories->count() > 0)
                                    @foreach($post->categories as $cat)
                                        <a href="{{ route('categories.show', $cat->slug) }}" class="badge-gradient {{ $cat->id === $category->id ? 'bg-gradient-to-r from-indigo-600 to-purple-600 ring-2 ring-indigo-400' : 'bg-gradient-to-r from-indigo-500 to-purple-500' }} text-white hover:from-indigo-600 hover:to-purple-600">
                                            {{ $cat->name }}
                                        </a>
                                    @endforeach
                                @endif

                                @if($post->tags->count() > 0)
                                    @foreach($post->tags as $tag)
                                        <a href="{{ route('tags.show', $tag->slug) }}" class="badge-gradient bg-gradient-to-r from-pink-500 to-rose-500 text-white hover:from-pink-600 hover:to-rose-600">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Read More Link -->
                            <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-pink-600 dark:hover:text-pink-400 font-medium group-hover:translate-x-1 transition-all duration-300">
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
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-lg">No posts in this category yet. Check back soon!</p>
            </div>
        @endif
    </div>
</x-public-layout>

