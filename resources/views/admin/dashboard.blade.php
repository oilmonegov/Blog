<x-admin-layout>
    <x-slot name="header">
        <div class="rounded-3xl bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 text-white p-6 lg:p-8 shadow-brand-glow">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-wide text-white/80">Hello {{ auth()->user()->name }}</p>
                    <h2 class="text-3xl font-bold">Admin Dashboard</h2>
                    <p class="text-white/80 mt-2">Insights for {{ $currentDate->format('l, F j') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                    <div class="bg-white/20 rounded-2xl p-4 backdrop-blur-sm">
                        <p class="text-sm text-white/80">Posts this week</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($postsThisWeek) }}</p>
                        <p class="text-xs mt-1 {{ $postsGrowth >= 0 ? 'text-green-100' : 'text-red-100' }}">
                            {{ $postsGrowth >= 0 ? '+' : '' }}{{ $postsGrowth }}% vs last week
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-2xl p-4 backdrop-blur-sm">
                        <p class="text-sm text-white/80">Comments this week</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($commentsThisWeek) }}</p>
                        <p class="text-xs mt-1 {{ $commentsGrowth >= 0 ? 'text-green-100' : 'text-red-100' }}">
                            {{ $commentsGrowth >= 0 ? '+' : '' }}{{ $commentsGrowth }}% vs last week
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-10">
        <!-- Quick Actions -->
        <section>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($quickActions as $action)
                    <a href="{{ $action['url'] }}" class="admin-quick-action bg-gradient-to-r {{ $action['color'] }}">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20">
                                @switch($action['icon'])
                                    @case('plus')
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        @break
                                    @case('folder')
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                        </svg>
                                        @break
                                    @case('collection')
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        @break
                                    @case('chat')
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v11a2 2 0 01-2 2h-6z" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                @endswitch
                            </span>
                            <div class="text-left">
                                <p class="text-lg font-semibold">{{ $action['label'] }}</p>
                                <p class="text-sm text-white/80">{{ $action['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Statistic Cards -->
        <section>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($statCards as $card)
                    <div class="admin-stat-card p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($card['value']) }}</p>
                            </div>
                            <div class="h-14 w-14 rounded-2xl text-white flex items-center justify-center {{ $card['icon_bg'] }}">
                                @switch($card['icon'])
                                    @case('published')
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @break
                                    @case('draft')
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @break
                                    @case('category')
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h6" />
                                        </svg>
                                        @break
                                    @case('comment')
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v11a2 2 0 01-2 2h-6z" />
                                        </svg>
                                        @break
                                    @case('user')
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.364 4.56m-2.121 13.243A5 5 0 0012 7c-1.657 0-3 1.567-3 3.5" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                @endswitch
                            </div>
                        </div>
                        @if(! is_null($card['change']))
                            <p class="mt-4 text-sm font-medium {{ $card['change'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $card['change'] >= 0 ? '+' : '' }}{{ $card['change'] }}% vs last week
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Highlight Cards -->
        <section>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="admin-card-gradient p-5">
                    <p class="text-sm text-gray-500">Most active author</p>
                    @if($topAuthor)
                        <p class="text-lg font-semibold mt-2">{{ $topAuthor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $topAuthor->posts_count }} published posts</p>
                    @else
                        <p class="text-lg font-semibold mt-2 text-gray-500">Not enough data yet</p>
                    @endif
                </div>
                <div class="admin-card-gradient p-5">
                    <p class="text-sm text-gray-500">Top performing post</p>
                    @if($topPost)
                        <a href="{{ route('admin.posts.show', $topPost) }}" class="text-lg font-semibold mt-2 text-indigo-600 hover:text-indigo-800">
                            {{ $topPost->title }}
                        </a>
                        <p class="text-sm text-gray-500">{{ $topPost->comments_count }} comments</p>
                    @else
                        <p class="text-lg font-semibold mt-2 text-gray-500">Not enough data yet</p>
                    @endif
                </div>
                <div class="admin-card-gradient p-5">
                    <p class="text-sm text-gray-500">Average posts per day</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($avgPostsPerDay, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ number_format($postsThisMonth) }} posts • {{ number_format($commentsThisMonth) }} comments this month</p>
                </div>
            </div>
        </section>

        <!-- Charts & Category Breakdown -->
        <section>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="admin-chart-container lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">7-day engagement trend</h3>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-purple-500"></span>Posts</span>
                            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-pink-500"></span>Comments</span>
                        </div>
                    </div>
                    @php
                        $maxTrendValue = max(1, $trendData->max(fn ($day) => max($day['posts'], $day['comments'])));
                        $hasTrendData = $trendData->sum(fn ($day) => $day['posts'] + $day['comments']) > 0;
                    @endphp
                    @if($hasTrendData)
                        <div class="flex items-end gap-4 h-48">
                            @foreach($trendData as $day)
                                @php
                                    $postsHeight = max(6, ($day['posts'] / $maxTrendValue) * 150);
                                    $commentsHeight = max(6, ($day['comments'] / $maxTrendValue) * 150);
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="flex items-end gap-1 w-full">
                                        <div class="flex-1 bg-gradient-to-t from-purple-500/30 to-purple-500 rounded-xl" style="height: {{ $postsHeight }}px"></div>
                                        <div class="flex-1 bg-gradient-to-t from-pink-500/30 to-pink-500 rounded-xl" style="height: {{ $commentsHeight }}px"></div>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $day['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-3">
                            <div class="skeleton h-4 w-full"></div>
                            <div class="skeleton h-4 w-5/6"></div>
                            <div class="skeleton h-4 w-2/3"></div>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">No engagement data yet. Publish content to see trends.</p>
                    @endif
                </div>
                <div class="admin-chart-container">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Category distribution</h3>
                    @php
                        $categoryTotal = max(1, $categoryBreakdown->sum('count'));
                    @endphp
                    <div class="space-y-4">
                        @forelse($categoryBreakdown as $category)
                            @php
                                $percentage = round(($category['count'] / $categoryTotal) * 100);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm">
                                    <p class="font-medium text-gray-900">{{ $category['name'] }}</p>
                                    <p class="text-gray-500">{{ $category['count'] }} posts</p>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Add categories to see distribution insights.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- Pending Items -->
        <section>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="admin-card-gradient p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Drafts awaiting review</h3>
                        <span class="text-sm font-semibold text-gray-500">{{ $pendingDrafts->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        @forelse($pendingDrafts as $draft)
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('admin.posts.edit', $draft) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $draft->title }}
                                    </a>
                                    <p class="text-xs text-gray-500">by {{ $draft->author?->name ?? 'Unknown' }} • {{ $draft->updated_at?->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('admin.posts.edit', $draft) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Review</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">All drafts are published. Great job!</p>
                        @endforelse
                    </div>
                </div>
                <div class="admin-card-gradient p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Comments to check</h3>
                        <span class="text-sm font-semibold text-gray-500">{{ $pendingComments->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        @forelse($pendingComments as $comment)
                            <div>
                                <p class="text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($comment->content, 80) }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    by {{ $comment->user?->name ?? 'Unknown' }} on
                                    <a href="{{ route('admin.posts.show', $comment->post) }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ \Illuminate\Support\Str::limit($comment->post->title, 40) }}
                                    </a>
                                    • {{ $comment->created_at?->diffForHumans() }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No comments waiting for attention.</p>
                        @endforelse
                    </div>
                </div>
                <div class="admin-card-gradient p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Categories needing content</h3>
                        <span class="text-sm font-semibold text-gray-500">{{ $orphanCategories->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        @forelse($orphanCategories as $category)
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500">Created {{ $category->created_at?->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Assign posts</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">All categories are in use.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Posts & Comments -->
        <section>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="admin-card-gradient p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent posts</h3>
                        <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
                    </div>
                    @if($recentPosts->count())
                        <div class="space-y-4">
                            @foreach($recentPosts as $post)
                                <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div>
                                        <a href="{{ route('admin.posts.show', $post) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $post->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">
                                            by {{ $post->author?->name ?? 'Unknown' }} • {{ $post->created_at?->diffForHumans() }}
                                        </p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($post->categories as $category)
                                                <span class="badge-gradient bg-gradient-to-r from-purple-500 to-pink-500 text-white">{{ $category->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $post->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $post->status->label() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No posts published yet.</p>
                    @endif
                </div>
                <div class="admin-card-gradient p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Comments</h3>
                        <a href="{{ route('admin.comments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Manage</a>
                    </div>
                    @if($recentComments->count())
                        <div class="space-y-4">
                            @foreach($recentComments as $comment)
                                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <p class="text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($comment->content, 120) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        by {{ $comment->user?->name ?? 'Unknown' }} on
                                        <a href="{{ route('admin.posts.show', $comment->post) }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ \Illuminate\Support\Str::limit($comment->post->title, 40) }}
                                        </a>
                                        • {{ $comment->created_at?->diffForHumans() }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No comments yet.</p>
                    @endif
                </div>
            </div>
        </section>

        <!-- Activity Timeline -->
        <section>
            <div class="admin-card-gradient p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Activity timeline</h3>
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all activity</a>
                </div>
                <div class="admin-timeline space-y-6">
                    @php
                        $activityColors = [
                            'post_published' => 'bg-purple-100 text-purple-700',
                            'comment' => 'bg-pink-100 text-pink-700',
                            'category_created' => 'bg-blue-100 text-blue-700',
                            'user_registered' => 'bg-indigo-100 text-indigo-700',
                        ];
                    @endphp
                    @forelse($activities as $activity)
                        <div class="admin-activity-item">
                            <div class="mt-1">
                                @php
                                    $color = $activityColors[$activity['type']] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold {{ $color }}">
                                    {{ strtoupper(substr($activity['type'], 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-gray-900">{{ $activity['title'] }}</p>
                                    <span class="text-xs text-gray-400">
                                        {{ $activity['time'] instanceof \Carbon\CarbonInterface ? $activity['time']->diffForHumans() : $activity['time'] }}
                                    </span>
                                </div>
                                @if(!empty($activity['description']))
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ \Illuminate\Support\Str::limit($activity['description'], 120) }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">by {{ $activity['user'] }}</p>
                                @if(!empty($activity['url']) && $activity['url'] !== '#')
                                    <a href="{{ $activity['url'] }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-2 inline-flex items-center">
                                        View details →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No recent activity yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-admin-layout>
