<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->author = User::factory()->create(['role' => UserRole::AUTHOR]);
});

test('admin can view post index', function () {
    Post::factory()->count(3)->create();

    actingAs($this->admin)
        ->get(route('admin.posts.index'))
        ->assertOk()
        ->assertSee('Post Management');
});

test('admin can view create post form', function () {
    actingAs($this->admin)
        ->get(route('admin.posts.create'))
        ->assertOk()
        ->assertSee('Create Post');
});

test('admin can create a post', function () {
    $category = Category::factory()->create();

    actingAs($this->admin)
        ->post(route('admin.posts.store'), [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'status' => PostStatus::DRAFT->value,
            'categories' => [$category->id],
            'tags' => 'laravel, php',
        ])
        ->assertRedirect(route('admin.posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'status' => PostStatus::DRAFT->value,
    ]);

    $post = Post::where('title', 'Test Post')->first();
    expect($post->categories)->toHaveCount(1);
    expect($post->tags)->toHaveCount(2);
});

test('admin can view a post', function () {
    $post = Post::factory()->create();

    actingAs($this->admin)
        ->get(route('admin.posts.show', $post))
        ->assertOk()
        ->assertSee($post->title);
});

test('admin can view edit post form', function () {
    $post = Post::factory()->create();

    actingAs($this->admin)
        ->get(route('admin.posts.edit', $post))
        ->assertOk()
        ->assertSee('Edit Post');
});

test('admin can update a post', function () {
    $post = Post::factory()->create(['title' => 'Old Title']);
    $category = Category::factory()->create();

    actingAs($this->admin)
        ->put(route('admin.posts.update', $post), [
            'title' => 'New Title',
            'content' => $post->content,
            'status' => $post->status->value,
            'slug' => $post->slug, // Keep existing slug
            'categories' => [$category->id],
            'tags' => 'updated, tags',
        ])
        ->assertRedirect(route('admin.posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'New Title',
    ]);
});

test('admin can delete a post', function () {
    $post = Post::factory()->create();

    actingAs($this->admin)
        ->delete(route('admin.posts.destroy', $post))
        ->assertRedirect(route('admin.posts.index'));

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

test('admin can publish a post', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::DRAFT,
        'published_at' => null,
    ]);

    actingAs($this->admin)
        ->patch(route('admin.posts.publish', $post))
        ->assertRedirect();

    $post->refresh();
    expect($post->status)->toBe(PostStatus::PUBLISHED);
    expect($post->published_at)->not->toBeNull();
});

test('admin can unpublish a post', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    actingAs($this->admin)
        ->patch(route('admin.posts.publish', $post))
        ->assertRedirect();

    $post->refresh();
    expect($post->status)->toBe(PostStatus::DRAFT);
    expect($post->published_at)->toBeNull();
});

test('admin can filter posts by status', function () {
    Post::factory()->create(['status' => PostStatus::PUBLISHED]);
    Post::factory()->create(['status' => PostStatus::DRAFT]);

    actingAs($this->admin)
        ->get(route('admin.posts.index', ['status' => PostStatus::PUBLISHED->value]))
        ->assertOk()
        ->assertSee('Published');
});

test('admin can search posts', function () {
    Post::factory()->create(['title' => 'Laravel Tutorial']);
    Post::factory()->create(['title' => 'PHP Basics']);

    actingAs($this->admin)
        ->get(route('admin.posts.index', ['search' => 'Laravel']))
        ->assertOk()
        ->assertSee('Laravel Tutorial')
        ->assertDontSee('PHP Basics');
});

test('author cannot access admin post routes', function () {
    actingAs($this->author)
        ->get(route('admin.posts.index'))
        ->assertForbidden();
});

