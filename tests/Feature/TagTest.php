<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('public can view tag archive page', function () {
    /** @var \Tests\TestCase $this */
    $tag = Tag::factory()->create(['slug' => 'laravel']);
    $post = Post::factory()->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    $post->tags()->attach($tag);

    $response = $this->get(route('tags.show', $tag->slug));

    $response->assertStatus(200);
    $response->assertViewHas('tag');
    $response->assertViewHas('posts');
    expect($response->viewData('tag')->id)->toBe($tag->id);
});

test('tag archive page shows only published posts', function () {
    /** @var \Tests\TestCase $this */
    $tag = Tag::factory()->create(['slug' => 'laravel']);
    $publishedPost = Post::factory()->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    $draftPost = Post::factory()->create([
        'status' => PostStatus::DRAFT,
    ]);
    $publishedPost->tags()->attach($tag);
    $draftPost->tags()->attach($tag);

    $response = $this->get(route('tags.show', $tag->slug));

    $response->assertStatus(200);
    $posts = $response->viewData('posts');
    expect($posts->count())->toBe(1);
    expect($posts->first()->id)->toBe($publishedPost->id);
});

test('tag archive page paginates posts', function () {
    /** @var \Tests\TestCase $this */
    $tag = Tag::factory()->create(['slug' => 'laravel']);
    $posts = Post::factory()->count(15)->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    foreach ($posts as $post) {
        $post->tags()->attach($tag);
    }

    $response = $this->get(route('tags.show', $tag->slug));

    $response->assertStatus(200);
    $posts = $response->viewData('posts');
    expect($posts->count())->toBe(10); // Default pagination
    expect($posts->hasMorePages())->toBeTrue();
});

test('tag archive page returns 404 for non-existent tag', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('tags.show', 'non-existent'));

    $response->assertStatus(404);
});

test('tags are auto-created when added to posts', function () {
    /** @var \Tests\TestCase $this */
    $admin = User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
    $post = Post::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'title' => $post->title,
        'content' => $post->content,
        'status' => $post->status->value,
        'slug' => $post->slug,
        'tags' => 'laravel, php, web-development',
    ]);

    expect(Tag::where('name', 'laravel')->exists())->toBeTrue();
    expect(Tag::where('name', 'php')->exists())->toBeTrue();
    expect(Tag::where('name', 'web-development')->exists())->toBeTrue();

    $post->refresh();
    expect($post->tags->count())->toBe(3);
});

test('tag slug is auto-generated when created via post', function () {
    /** @var \Tests\TestCase $this */
    $admin = User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
    $post = Post::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'title' => $post->title,
        'content' => $post->content,
        'status' => $post->status->value,
        'slug' => $post->slug,
        'tags' => 'Web Development',
    ]);

    $tag = Tag::where('name', 'Web Development')->first();
    expect($tag->slug)->toBe('web-development');
});

test('duplicate tags are not created when added to posts', function () {
    /** @var \Tests\TestCase $this */
    $admin = User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
    $post = Post::factory()->create(['author_id' => $admin->id]);
    $existingTag = Tag::factory()->create(['name' => 'laravel']);

    $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'title' => $post->title,
        'content' => $post->content,
        'status' => $post->status->value,
        'slug' => $post->slug,
        'tags' => 'laravel, php',
    ]);

    // Should not create duplicate tag
    expect(Tag::where('name', 'laravel')->count())->toBe(1);
    expect(Tag::where('name', 'php')->exists())->toBeTrue();

    $post->refresh();
    expect($post->tags->count())->toBe(2);
});
