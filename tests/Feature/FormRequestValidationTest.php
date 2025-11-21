<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('StorePostRequest validation', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    });

    test('title is required', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'content' => 'Test content',
                'status' => PostStatus::DRAFT->value,
            ])
            ->assertSessionHasErrors('title');
    });

    test('title must not exceed 255 characters', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => str_repeat('a', 256),
                'content' => 'Test content',
                'status' => PostStatus::DRAFT->value,
            ])
            ->assertSessionHasErrors('title');
    });

    test('content is required', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'status' => PostStatus::DRAFT->value,
            ])
            ->assertSessionHasErrors('content');
    });

    test('status is required', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'content' => 'Test content',
            ])
            ->assertSessionHasErrors('status');
    });

    test('status must be valid enum value', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'content' => 'Test content',
                'status' => 'invalid-status',
            ])
            ->assertSessionHasErrors('status');
    });

    test('slug must be unique', function () {
        Post::factory()->create(['slug' => 'existing-slug']);

        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'content' => 'Test content',
                'status' => PostStatus::DRAFT->value,
                'slug' => 'existing-slug',
            ])
            ->assertSessionHasErrors('slug');
    });

    test('categories must exist', function () {
        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'content' => 'Test content',
                'status' => PostStatus::DRAFT->value,
                'categories' => [99999],
            ])
            ->assertSessionHasErrors('categories.0');
    });

    test('valid post data passes validation', function () {
        $category = Category::factory()->create();

        actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test Title',
                'content' => 'Test content',
                'status' => PostStatus::DRAFT->value,
                'categories' => [$category->id],
            ])
            ->assertSessionHasNoErrors();
    });
});

describe('UpdatePostRequest validation', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->post = Post::factory()->create(['author_id' => $this->admin->id]);
    });

    test('slug must be unique except for current post', function () {
        $otherPost = Post::factory()->create(['slug' => 'other-slug']);

        actingAs($this->admin)
            ->put(route('admin.posts.update', $this->post), [
                'title' => 'Updated Title',
                'content' => 'Updated content',
                'status' => PostStatus::DRAFT->value,
                'slug' => 'other-slug',
            ])
            ->assertSessionHasErrors('slug');
    });

    test('slug can be same as current post', function () {
        actingAs($this->admin)
            ->put(route('admin.posts.update', $this->post), [
                'title' => 'Updated Title',
                'content' => 'Updated content',
                'status' => PostStatus::DRAFT->value,
                'slug' => $this->post->slug,
            ])
            ->assertSessionHasNoErrors();
    });
});

describe('StoreCommentRequest validation', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create([
            'status' => PostStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    });

    test('content is required', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'post_id' => $this->post->id,
            ])
            ->assertSessionHasErrors('content');
    });

    test('content must be at least 3 characters', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'content' => 'ab',
                'post_id' => $this->post->id,
            ])
            ->assertSessionHasErrors('content');
    });

    test('content must not exceed 5000 characters', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'content' => str_repeat('a', 5001),
                'post_id' => $this->post->id,
            ])
            ->assertSessionHasErrors('content');
    });

    test('post_id is required', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'content' => 'Valid comment content',
            ])
            ->assertSessionHasErrors('post_id');
    });

    test('post_id must exist', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'content' => 'Valid comment content',
                'post_id' => 99999,
            ])
            ->assertSessionHasErrors('post_id');
    });

    test('valid comment data passes validation', function () {
        actingAs($this->user)
            ->post(route('comments.store'), [
                'content' => 'This is a valid comment',
                'post_id' => $this->post->id,
            ])
            ->assertSessionHasNoErrors();
    });
});

