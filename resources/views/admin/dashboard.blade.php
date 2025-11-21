<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Posts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Posts</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_posts }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Published Posts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Published</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $published_posts }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Draft Posts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Drafts</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $draft_posts }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Categories</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_categories }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-6">
        <!-- Total Comments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Comments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_comments }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_users }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <!-- Recent Posts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
                @if($recent_posts->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_posts as $post)
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                <div class="flex-1">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ $post->title }}
                                    </a>
                                    <div class="mt-1 text-xs text-gray-500">
                                        by {{ $post->author->name }} • {{ $post->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="ml-4 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $post->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $post->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            View all posts →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No posts yet.</p>
                @endif
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Comments</h3>
                @if($recent_comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_comments as $comment)
                            <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                <div class="text-sm text-gray-900">
                                    {{ \Illuminate\Support\Str::limit($comment->content, 80) }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    by {{ $comment->user->name }} on 
                                    <a href="{{ route('admin.posts.show', $comment->post) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ \Illuminate\Support\Str::limit($comment->post->title, 40) }}
                                    </a>
                                    • {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.comments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            View all comments →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No comments yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

