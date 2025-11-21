<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('post detail page displays published post', function () {
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
        'content' => 'Test post content for display',
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee($post->title);
    $response->assertSee('Test post content', false);
    $response->assertSee($author->name);
});

test('post detail page returns 404 for draft post', function () {
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::DRAFT,
        'published_at' => null,
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(404);
});

test('post detail page returns 404 for non-existent post', function () {
    $response = $this->get(route('posts.show', 'non-existent-slug'));

    $response->assertStatus(404);
});

test('post detail page displays categories and tags', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Technology']);
    $tag = Tag::factory()->create(['name' => 'Laravel']);

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $post->categories()->attach($category);
    $post->tags()->attach($tag);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee($category->name);
    $response->assertSee($tag->name);
});

test('post detail page displays approved comments', function () {
    $author = User::factory()->create();
    $commenter = User::factory()->create(['name' => 'Commenter']);

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $approvedComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'content' => 'This is an approved comment',
        'approved_at' => now(),
    ]);

    // Create unapproved comment (should not appear)
    // Note: Comment model auto-approves, so we need to update after creation
    $unapprovedComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'content' => 'This is an unapproved comment',
    ]);
    $unapprovedComment->update(['approved_at' => null]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee('This is an approved comment', false);
    $response->assertSee($commenter->name);
    $response->assertDontSee('This is an unapproved comment', false);
});

test('post detail page displays comment form for authenticated users', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee('Leave a Comment');
});

test('post detail page shows login prompt for unauthenticated users', function () {
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee('log in');
});

test('post detail page includes SEO meta tags', function () {
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
        'meta_title' => 'Custom Meta Title',
        'meta_description' => 'Custom meta description for SEO',
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee('Custom Meta Title', false);
    $response->assertSee('Custom meta description for SEO', false);
});

test('post detail page uses title and excerpt as fallback for SEO', function () {
    $author = User::factory()->create();

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
        'meta_title' => null,
        'meta_description' => null,
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee($post->title, false);
    if ($post->excerpt) {
        $response->assertSee($post->excerpt, false);
    }
});
