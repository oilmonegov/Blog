<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $post = Post::findOrFail($request->post_id);

        // Verify user can view the post (authorization already checked in request)
        if (! $request->user()->can('view', $post)) {
            abort(403, 'You cannot comment on this post.');
        }

        $comment = Comment::create([
            'content' => $request->content,
            'post_id' => $request->post_id,
            'user_id' => $request->user()->id,
            // approved_at is set automatically in the model's boot method
        ]);

        return redirect()->back()
            ->with('success', 'Comment posted successfully.');
    }
}
