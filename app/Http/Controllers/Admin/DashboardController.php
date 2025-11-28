<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Post::class);

        // Cache key prefix
        $cacheKey = 'admin.dashboard.';
        $cacheDuration = Carbon::now()->addMinutes(10);

        // Basic statistics
        $stats = Cache::remember($cacheKey . 'stats', $cacheDuration, function () {
            return [
            'total_posts' => Post::count(),
                'published_posts' => Post::where('status', PostStatus::PUBLISHED)->count(),
                'draft_posts' => Post::where('status', PostStatus::DRAFT)->count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'total_users' => User::count(),
            ];
        });

        // Weekly and monthly statistics
        $periodStats = Cache::remember($cacheKey . 'period_stats', $cacheDuration, function () {
            return [
                'posts_this_week' => Post::where('status', PostStatus::PUBLISHED)
                    ->where('published_at', '>=', Carbon::now()->startOfWeek())
                    ->count(),
                'posts_this_month' => Post::where('status', PostStatus::PUBLISHED)
                    ->where('published_at', '>=', Carbon::now()->startOfMonth())
                    ->count(),
                'comments_this_week' => Comment::where('created_at', '>=', Carbon::now()->startOfWeek())
                    ->count(),
                'comments_this_month' => Comment::where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->count(),
            ];
        });

        // Growth metrics
        $growthMetrics = Cache::remember($cacheKey . 'growth', $cacheDuration, function () use ($periodStats) {
            $lastWeekPosts = Post::where('status', PostStatus::PUBLISHED)
                ->whereBetween('published_at', [
                    Carbon::now()->subWeeks(2)->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ])
                ->count();
            
            $postsGrowth = $lastWeekPosts > 0 
                ? round((($periodStats['posts_this_week'] - $lastWeekPosts) / $lastWeekPosts) * 100, 1)
                : ($periodStats['posts_this_week'] > 0 ? 100 : 0);

            $lastWeekComments = Comment::whereBetween('created_at', [
                Carbon::now()->subWeeks(2)->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])->count();
            
            $commentsGrowth = $lastWeekComments > 0
                ? round((($periodStats['comments_this_week'] - $lastWeekComments) / $lastWeekComments) * 100, 1)
                : ($periodStats['comments_this_week'] > 0 ? 100 : 0);

            // Average posts per day
            $daysSinceFirstPost = Post::where('status', PostStatus::PUBLISHED)
                ->whereNotNull('published_at')
                ->orderBy('published_at', 'asc')
                ->first();
            
            $avgPostsPerDay = 0;
            $publishedPosts = Post::where('status', PostStatus::PUBLISHED)->count();
            if ($daysSinceFirstPost && $publishedPosts > 0) {
                $daysDiff = max(1, Carbon::now()->diffInDays($daysSinceFirstPost->published_at));
                $avgPostsPerDay = round($publishedPosts / $daysDiff, 2);
            }

            return [
                'posts_growth' => $postsGrowth,
                'comments_growth' => $commentsGrowth,
                'avg_posts_per_day' => $avgPostsPerDay,
            ];
        });

        // Top performing content (Not cached as it might change frequently and is specific)
        // Or short cache
        $topPerformers = Cache::remember($cacheKey . 'top_performers', $cacheDuration, function () {
            return [
                'most_commented_post' => Post::withCount('comments')
                    ->orderBy('comments_count', 'desc')
                    ->first(),
                'most_active_author' => User::withCount(['posts' => function ($query) {
                    $query->where('status', PostStatus::PUBLISHED);
                }])
                    ->orderBy('posts_count', 'desc')
                    ->first(),
            ];
        });

        // Time-series data for charts
        $chartsData = Cache::remember($cacheKey . 'charts', $cacheDuration, function () {
            $postsTimeSeries = Post::where('status', PostStatus::PUBLISHED)
                ->where('published_at', '>=', Carbon::now()->subDays(30))
                ->select(
                    DB::raw('DATE(published_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->pluck('count', 'date')
                ->toArray();

            $commentsTimeSeries = Comment::where('created_at', '>=', Carbon::now()->subDays(30))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->pluck('count', 'date')
                ->toArray();

            // Fill in missing dates with 0
            $postsChartData = [];
            $commentsChartData = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $postsChartData[$date] = $postsTimeSeries[$date] ?? 0;
                $commentsChartData[$date] = $commentsTimeSeries[$date] ?? 0;
            }

            return [
                'posts_chart_data' => $postsChartData,
                'comments_chart_data' => $commentsChartData,
                'max_posts' => max(array_values($postsChartData)) ?: 1,
                'max_comments' => max(array_values($commentsChartData)) ?: 1,
            ];
        });

        // Category Distribution (New)
        $categoryDistribution = Cache::remember($cacheKey . 'category_distribution', $cacheDuration, function () {
            return Category::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'count' => $category->posts_count,
                    ];
                });
        });

        // Pending items (Not cached or very short cache as these are actionable)
        $pendingDrafts = Post::where('status', PostStatus::DRAFT)
            ->with('author')
            ->latest()
            ->take(5)
            ->get();

        $recentUnmoderatedComments = Comment::whereNull('approved_at')
            ->with(['user', 'post'])
            ->latest()
            ->take(5)
            ->get();

        $orphanedCategories = Category::doesntHave('posts')
                ->latest()
            ->take(5)
            ->get();

        // Activity feed
        $activityFeed = Cache::remember($cacheKey . 'activity_feed', Carbon::now()->addMinutes(5), function () {
            // Recent post publications
            $recentPublishedPosts = Post::where('status', PostStatus::PUBLISHED)
                ->with('author')
                ->whereNotNull('published_at')
                ->orderBy('published_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'type' => 'post_published',
                        'title' => $post->title,
                        'user' => $post->author->name,
                        'time' => $post->published_at,
                        'description' => 'Published by '.$post->author->name,
                        'icon' => 'post',
                        'url' => route('admin.posts.show', $post),
                    ];
                })
                ->values()
                ->toBase();

            // Recent comments
            $recentComments = Comment::with(['user', 'post'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($comment) {
                    return [
                        'type' => 'comment',
                        'title' => 'Commented on: ' . $comment->post->title,
                        'user' => $comment->user->name,
                        'time' => $comment->created_at,
                        'description' => $comment->content,
                        'icon' => 'comment',
                        'url' => route('admin.posts.show', $comment->post),
                    ];
                })
                ->values()
                ->toBase();

            // Recent category creations
            $recentCategories = Category::orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'type' => 'category_created',
                        'title' => 'Created category: ' . $category->name,
                        'user' => 'System',
                        'time' => $category->created_at,
                        'description' => 'New category ready for tagging',
                        'icon' => 'category',
                        'url' => route('admin.categories.index'),
                    ];
                })
                ->values()
                ->toBase();

            // Recent user registrations (New)
            $recentUsers = User::orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user_registered',
                        'title' => 'New user registered',
                        'user' => $user->name,
                        'time' => $user->created_at,
                        'description' => $user->email,
                        'icon' => 'user',
                        'url' => '#', // No user show page yet
                    ];
                })
                ->values()
                ->toBase();

            return $recentPublishedPosts
                ->merge($recentComments)
                ->merge($recentCategories)
                ->merge($recentUsers)
                ->sortByDesc('time')
                ->take(10)
                ->values();
        });

        // Recent posts and comments for existing sections
        $recentPosts = Post::with(['author', 'categories'])
            ->latest()
            ->take(5)
            ->get();
        $recentCommentsList = Comment::with(['user', 'post'])
            ->latest()
            ->take(5)
            ->get();

        $pendingComments = $recentUnmoderatedComments->isEmpty()
            ? Comment::with(['user', 'post'])->latest()->take(5)->get()
            : $recentUnmoderatedComments;

        $statCards = [
            [
                'label' => 'Total Posts',
                'value' => $stats['total_posts'],
                'change' => $growthMetrics['posts_growth'],
                'icon' => 'post',
                'icon_bg' => 'stat-icon-bg-purple',
            ],
            [
                'label' => 'Published Posts',
                'value' => $stats['published_posts'],
                'change' => null,
                'icon' => 'published',
                'icon_bg' => 'stat-icon-bg-green',
            ],
            [
                'label' => 'Draft Posts',
                'value' => $stats['draft_posts'],
                'change' => null,
                'icon' => 'draft',
                'icon_bg' => 'stat-icon-bg-yellow',
            ],
            [
                'label' => 'Categories',
                'value' => $stats['total_categories'],
                'change' => null,
                'icon' => 'category',
                'icon_bg' => 'stat-icon-bg-blue',
            ],
            [
                'label' => 'Total Comments',
                'value' => $stats['total_comments'],
                'change' => $growthMetrics['comments_growth'],
                'icon' => 'comment',
                'icon_bg' => 'stat-icon-bg-pink',
            ],
            [
                'label' => 'Total Users',
                'value' => $stats['total_users'],
                'change' => null,
                'icon' => 'user',
                'icon_bg' => 'stat-icon-bg-indigo',
            ],
        ];

        $quickActions = [
            [
                'label' => 'Create Post',
                'description' => 'Publish a new story',
                'url' => route('admin.posts.create'),
                'color' => 'from-purple-600 to-pink-500',
                'icon' => 'plus',
            ],
            [
                'label' => 'Create Category',
                'description' => 'Organize your content',
                'url' => route('admin.categories.create'),
                'color' => 'from-blue-500 to-cyan-500',
                'icon' => 'folder',
            ],
            [
                'label' => 'View All Posts',
                'description' => 'Manage published work',
                'url' => route('admin.posts.index'),
                'color' => 'from-indigo-500 to-purple-500',
                'icon' => 'collection',
            ],
            [
                'label' => 'View Comments',
                'description' => 'Moderate discussions',
                'url' => route('admin.comments.index'),
                'color' => 'from-pink-500 to-rose-500',
                'icon' => 'chat',
            ],
        ];

        $trendPeriod = CarbonPeriod::create(
            Carbon::now()->subDays(6)->startOfDay(),
            '1 day',
            Carbon::now()->endOfDay()
        );

        $trendData = collect($trendPeriod)->map(function (CarbonInterface $date) use ($chartsData) {
            $key = $date->format('Y-m-d');

            return [
                'label' => $date->format('M j'),
                'posts' => (int) ($chartsData['posts_chart_data'][$key] ?? 0),
                'comments' => (int) ($chartsData['comments_chart_data'][$key] ?? 0),
            ];
        });

        $categoryBreakdown = collect($categoryDistribution);

        return view('admin.dashboard', [
            'statCards' => $statCards,
            'quickActions' => $quickActions,
            'trendData' => $trendData,
            'categoryBreakdown' => $categoryBreakdown,
            'pendingDrafts' => $pendingDrafts,
            'pendingComments' => $pendingComments,
            'orphanCategories' => $orphanedCategories,
            'activities' => $activityFeed,
            'topAuthor' => $topPerformers['most_active_author'],
            'topPost' => $topPerformers['most_commented_post'],
            'postsThisWeek' => $periodStats['posts_this_week'],
            'commentsThisWeek' => $periodStats['comments_this_week'],
            'postsThisMonth' => $periodStats['posts_this_month'],
            'commentsThisMonth' => $periodStats['comments_this_month'],
            'postsGrowth' => $growthMetrics['posts_growth'],
            'commentsGrowth' => $growthMetrics['comments_growth'],
            'avgPostsPerDay' => $growthMetrics['avg_posts_per_day'],
            'currentDate' => Carbon::now(),
            'recentPosts' => $recentPosts,
            'recentComments' => $recentCommentsList,
        ]);
    }
}
