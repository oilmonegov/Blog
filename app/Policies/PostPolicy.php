<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     * Admins see all, authors see own.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAuthor();
    }

    /**
     * Determine whether the user can view the model.
     * Public can view published, authors/admins can view own/all.
     */
    public function view(?User $user, Post $post): bool
    {
        // Public can view published posts
        if ($post->status === PostStatus::PUBLISHED && $post->published_at !== null) {
            return true;
        }

        // Must be authenticated to view unpublished posts
        if (! $user) {
            return false;
        }

        // Admins can view all posts
        if ($user->isAdmin()) {
            return true;
        }

        // Authors can view their own posts
        return $user->isAuthor() && $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * Authenticated users with author/admin role.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAuthor();
    }

    /**
     * Determine whether the user can update the model.
     * Authors can update own, admins can update all.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAuthor() && $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Authors can delete own, admins can delete all.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAuthor() && $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can publish the model.
     * Authors can publish own, admins can publish all.
     */
    public function publish(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAuthor() && $post->author_id === $user->id;
    }
}
