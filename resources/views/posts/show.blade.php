@php
    $metaTitle = $post->meta_title ?? $post->title;
    $metaDescription = $post->meta_description ?? $post->excerpt;
    $shareUrl = url()->current();
    $shareTitle = urlencode($post->title);
@endphp

<x-public-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :post="$post">
    <!-- Gradient Header Section -->
    <div class="bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 text-white py-12 md:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-2 text-sm text-purple-100 mb-4">
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->format('F j, Y') }}
                </time>
                <span>•</span>
                <span>{{ $post->reading_time }} min read</span>
            </div>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                {{ $post->title }}
            </h1>

            @if($post->categories->count() > 0 || $post->tags->count() > 0)
                <div class="flex flex-wrap items-center gap-3">
                    @if($post->categories->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->categories as $category)
                                <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($post->tags->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
            <!-- Share Buttons -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="avatar-gradient">
                            <div>
                                <span class="text-purple-600 dark:text-purple-400">
                                    {{ strtoupper(substr($post->author->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $post->author->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Published {{ $post->published_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Share:</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-300" aria-label="Share on Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-300" aria-label="Share on Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-full bg-blue-700 text-white hover:bg-blue-800 transition-colors duration-300" aria-label="Share on LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="p-8 prose-enhanced">
                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </article>

        <!-- Author Bio Section -->
        <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-6">
            <div class="flex items-start space-x-4">
                <div class="avatar-gradient flex-shrink-0">
                    <div>
                        <span class="text-purple-600 dark:text-purple-400 text-lg">
                            {{ strtoupper(substr($post->author->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                        {{ $post->author->name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Author of {{ $post->author->posts()->published()->count() }} {{ $post->author->posts()->published()->count() === 1 ? 'post' : 'posts' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Related Posts Section -->
        @if($relatedPosts->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                    Related Posts
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="card-gradient overflow-hidden group">
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-pink-500 group-hover:bg-clip-text group-hover:text-transparent transition-all duration-300">
                                    <a href="{{ route('posts.show', $relatedPost->slug) }}" class="hover:underline">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    <time datetime="{{ $relatedPost->published_at->toIso8601String() }}">
                                        {{ $relatedPost->published_at->format('M j, Y') }}
                                    </time>
                                    <span>•</span>
                                    <span>{{ $relatedPost->reading_time }} min read</span>
                                </div>
                                @if($relatedPost->excerpt)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 mb-3">
                                        {{ $relatedPost->excerpt }}
                                    </p>
                                @endif
                                <a href="{{ route('posts.show', $relatedPost->slug) }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-pink-600 dark:hover:text-pink-400 font-medium inline-flex items-center">
                                    Read more
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Comments Section -->
        <div class="mt-12">
            <x-comment-list :comments="$comments" :canDelete="false" />
            <x-comment-form :post="$post" />
        </div>

        <!-- Back to Blog Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-pink-600 dark:hover:text-pink-400 font-medium transition-colors duration-300">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Blog
            </a>
        </div>
    </div>
</x-public-layout>

