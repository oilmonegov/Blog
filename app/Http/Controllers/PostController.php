<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index(Request $request): View
    {
        $posts = Post::published()
            ->with(['author', 'categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): View
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['author', 'categories', 'tags'])
            ->firstOrFail();

        // Load approved comments separately
        $comments = $post->comments()
            ->whereNotNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        // Get related posts (same categories or tags, excluding current post)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                // Posts with same categories
                $categoryIds = $post->categories->pluck('id');
                if ($categoryIds->isNotEmpty()) {
                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    });
                }
                // Or posts with same tags
                $tagIds = $post->tags->pluck('id');
                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->with(['author', 'categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('posts.show', [
            'post' => $post,
            'comments' => $comments,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
