<?php

namespace App\Http\Controllers\Author;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class PublishPostController extends Controller
{
    /**
     * Publish or unpublish the post.
     */
    public function __invoke(Post $post): RedirectResponse
    {
        $this->authorize('publish', $post);

        // Ensure author can only publish their own posts
        if ($post->author_id !== request()->user()->id) {
            abort(403);
        }

        if ($post->status === PostStatus::PUBLISHED) {
            $post->update([
                'status' => PostStatus::DRAFT,
                'published_at' => null,
            ]);

            return redirect()->back()
                ->with('success', 'Post unpublished successfully.');
        }

        $post->update([
            'status' => PostStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Post published successfully.');
    }
}

