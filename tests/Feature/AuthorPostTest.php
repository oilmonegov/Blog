<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->author = User::factory()->create(['role' => UserRole::AUTHOR]);
    $this->otherAuthor = User::factory()->create(['role' => UserRole::AUTHOR]);
});

test('author can view own posts index', function () {
    Post::factory()->create(['author_id' => $this->author->id]);
    Post::factory()->create(['author_id' => $this->otherAuthor->id]);

    actingAs($this->author)
        ->get(route('author.posts.index'))
        ->assertOk();
    
    // Author should only see their own posts
    $response = actingAs($this->author)->get(route('author.posts.index'));
    expect($response->viewData('posts')->count())->toBe(1);
});

test('author can view create post form', function () {
    actingAs($this->author)
        ->get(route('author.posts.create'))
        ->assertOk()
        ->assertSee('Create Post');
});

test('author can create a post', function () {
    $category = Category::factory()->create();

    actingAs($this->author)
        ->post(route('author.posts.store'), [
            'title' => 'My Test Post',
            'content' => 'This is my test content',
            'status' => PostStatus::DRAFT->value,
            'categories' => [$category->id],
            'tags' => 'test, post',
        ])
        ->assertRedirect(route('author.posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'My Test Post',
        'author_id' => $this->author->id,
    ]);
});

test('author can view own post', function () {
    $post = Post::factory()->create(['author_id' => $this->author->id]);

    actingAs($this->author)
        ->get(route('author.posts.show', $post))
        ->assertOk()
        ->assertSee($post->title);
});

test('author cannot view other author post', function () {
    $post = Post::factory()->create(['author_id' => $this->otherAuthor->id]);

    actingAs($this->author)
        ->get(route('author.posts.show', $post))
        ->assertForbidden();
});

test('author can update own post', function () {
    $post = Post::factory()->create([
        'author_id' => $this->author->id,
        'title' => 'Old Title',
    ]);

    actingAs($this->author)
        ->put(route('author.posts.update', $post), [
            'title' => 'Updated Title',
            'content' => $post->content,
            'status' => $post->status->value,
            'slug' => $post->slug, // Keep existing slug
        ])
        ->assertRedirect(route('author.posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
    ]);
});

test('author cannot update other author post', function () {
    $post = Post::factory()->create(['author_id' => $this->otherAuthor->id]);

    actingAs($this->author)
        ->put(route('author.posts.update', $post), [
            'title' => 'Hacked Title',
            'content' => $post->content,
            'status' => $post->status->value,
        ])
        ->assertForbidden();
});

test('author can delete own post', function () {
    $post = Post::factory()->create(['author_id' => $this->author->id]);

    actingAs($this->author)
        ->delete(route('author.posts.destroy', $post))
        ->assertRedirect(route('author.posts.index'));

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

test('author cannot delete other author post', function () {
    $post = Post::factory()->create(['author_id' => $this->otherAuthor->id]);

    actingAs($this->author)
        ->delete(route('author.posts.destroy', $post))
        ->assertForbidden();
});

test('author can publish own post', function () {
    $post = Post::factory()->create([
        'author_id' => $this->author->id,
        'status' => PostStatus::DRAFT,
        'published_at' => null,
    ]);

    actingAs($this->author)
        ->patch(route('author.posts.publish', $post))
        ->assertRedirect();

    $post->refresh();
    expect($post->status)->toBe(PostStatus::PUBLISHED);
    expect($post->published_at)->not->toBeNull();
});

test('author cannot publish other author post', function () {
    $post = Post::factory()->create([
        'author_id' => $this->otherAuthor->id,
        'status' => PostStatus::DRAFT,
    ]);

    actingAs($this->author)
        ->patch(route('author.posts.publish', $post))
        ->assertForbidden();
});

