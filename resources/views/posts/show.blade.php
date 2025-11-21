@php
    $metaTitle = $post->meta_title ?? $post->title;
    $metaDescription = $post->meta_description ?? $post->excerpt;
@endphp

<x-public-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :post="$post">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Post Header -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                        {{ $post->published_at->format('F j, Y') }}
                    </time>
                    <span>•</span>
                    <span>By {{ $post->author->name }}</span>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>

                @if($post->categories->count() > 0 || $post->tags->count() > 0)
                    <div class="flex flex-wrap items-center gap-3">
                        @if($post->categories->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($post->categories as $category)
                                    <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors">
                                        {{ $category->name }}
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
                @endif
            </div>

            <!-- Post Content -->
            <div class="p-8 prose prose-lg max-w-none">
                <div class="text-gray-700 whitespace-pre-wrap">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <div class="mt-8">
            <x-comment-list :comments="$comments" :canDelete="false" />
            <x-comment-form :post="$post" />
        </div>

        <!-- Back to Blog Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Blog
            </a>
        </div>
    </div>
</x-public-layout>

