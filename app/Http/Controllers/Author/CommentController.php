<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display a listing of comments on own posts.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Comment::class);

        $user = $request->user();

        // Only show comments on posts authored by this user
        $query = Comment::with(['post', 'user'])
            ->whereHas('post', function ($q) use ($user) {
                $q->where('author_id', $user->id);
            })
            ->latest();

        // Filter by post
        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(15);

        return view('admin.comments.index', [
            'comments' => $comments,
        ]);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        // Ensure author can only delete comments on their own posts
        if ($comment->post->author_id !== $request->user()->id) {
            abort(403);
        }

        $comment->delete();

        return redirect()->route('author.comments.index')
            ->with('success', 'Comment deleted successfully.');
    }
}

