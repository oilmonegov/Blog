<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAuthor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Authors can view comments on their own posts
        return $user->isAuthor() && $comment->post->author_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can comment.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Only the comment author can update their own comment
        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Authors can delete on own posts, admins can delete any.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Authors can delete comments on their own posts
        return $user->isAuthor() && $comment->post->author_id === $user->id;
    }
}
