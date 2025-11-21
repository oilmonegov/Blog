<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Post::class);

        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'total_users' => User::count(),
            'recent_posts' => Post::with(['author', 'categories'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_comments' => Comment::with(['user', 'post'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $stats);
    }
}
