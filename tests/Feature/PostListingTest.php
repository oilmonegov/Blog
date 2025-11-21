<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('blog index page displays published posts', function () {
    $author = User::factory()->create();

    $publishedPost = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $draftPost = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::DRAFT,
        'published_at' => null,
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee($publishedPost->title);
    $response->assertDontSee($draftPost->title);
});

test('blog index page displays posts in latest first order', function () {
    $author = User::factory()->create();

    $olderPost = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now()->subDays(2),
    ]);

    $newerPost = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSeeInOrder([$newerPost->title, $olderPost->title]);
});

test('blog index page displays post previews with author and date', function () {
    $author = User::factory()->create(['name' => 'John Doe']);

    $post = Post::factory()->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee($post->title);
    $response->assertSee($author->name);
    $response->assertSee($post->published_at->format('F j, Y'));
});

test('blog index page displays categories and tags', function () {
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

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee($category->name);
    $response->assertSee($tag->name);
});

test('blog index page paginates posts', function () {
    $author = User::factory()->create();

    Post::factory()->count(15)->create([
        'author_id' => $author->id,
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    // Check that pagination links exist (Laravel pagination includes "Next" or page numbers)
    $response->assertSee('Next', false);
});

test('blog index page shows empty state when no posts', function () {
    $response = $this->get(route('posts.index'));

    $response->assertStatus(200);
    $response->assertSee('No posts available yet');
});
