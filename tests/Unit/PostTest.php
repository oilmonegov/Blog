<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

test('post generates slug from title on creation', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'My First Blog Post',
        'content' => 'This is the content of my first blog post.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->slug)->toBe('my-first-blog-post');
});

test('post generates unique slug when duplicate title exists', function () {
    $user = User::factory()->create();

    $post1 = Post::create([
        'title' => 'Duplicate Title',
        'content' => 'Content for first post.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $post2 = Post::create([
        'title' => 'Duplicate Title',
        'content' => 'Content for second post.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post1->slug)->toBe('duplicate-title');
    expect($post2->slug)->toBe('duplicate-title-1');
});

test('post generates unique slug with incrementing counter for multiple duplicates', function () {
    $user = User::factory()->create();

    $post1 = Post::create([
        'title' => 'Same Title',
        'content' => 'Content 1.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $post2 = Post::create([
        'title' => 'Same Title',
        'content' => 'Content 2.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $post3 = Post::create([
        'title' => 'Same Title',
        'content' => 'Content 3.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post1->slug)->toBe('same-title');
    expect($post2->slug)->toBe('same-title-1');
    expect($post3->slug)->toBe('same-title-2');
});

test('post generates slug from title with special characters', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Hello World! @#$%^&*()',
        'content' => 'Content with special characters.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    // Str::slug converts @ to 'at', so the result is 'hello-world-at'
    expect($post->slug)->toBe('hello-world-at');
});

test('post generates slug from title with unicode characters', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Café & Résumé',
        'content' => 'Content with unicode.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->slug)->toBe('cafe-resume');
});

test('post generates slug from title with multiple spaces', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Multiple    Spaces    Here',
        'content' => 'Content with multiple spaces.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->slug)->toBe('multiple-spaces-here');
});

test('post updates slug when title is changed', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Original Title',
        'content' => 'Content.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->slug)->toBe('original-title');

    $post->update([
        'title' => 'Updated Title',
    ]);

    expect($post->fresh()->slug)->toBe('updated-title');
});

test('post does not regenerate slug if slug is manually set', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Custom Slug Title',
        'slug' => 'custom-slug-value',
        'content' => 'Content.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->slug)->toBe('custom-slug-value');
});

test('post generates unique slug when updating to duplicate title', function () {
    $user = User::factory()->create();

    $post1 = Post::create([
        'title' => 'First Post',
        'content' => 'Content 1.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $post2 = Post::create([
        'title' => 'Second Post',
        'content' => 'Content 2.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post1->slug)->toBe('first-post');
    expect($post2->slug)->toBe('second-post');

    // Update post2 to have the same title as post1
    $post2->update([
        'title' => 'First Post',
    ]);

    // Post2 should get a unique slug
    expect($post2->fresh()->slug)->toBe('first-post-1');
});

test('post excludes itself when checking slug uniqueness on update', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'My Post',
        'content' => 'Content.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $originalSlug = $post->slug;
    expect($originalSlug)->toBe('my-post');

    // Update the post without changing the title
    $post->update([
        'content' => 'Updated content.',
    ]);

    // Slug should remain the same
    expect($post->fresh()->slug)->toBe($originalSlug);
});

test('post handles empty slug edge case', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Test Post',
        'slug' => '',
        'content' => 'Content.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    // Empty slug should be regenerated
    expect($post->slug)->not->toBeEmpty();
    expect($post->slug)->toBe('test-post');
});

test('post generates excerpt from content on creation', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Test Post',
        'content' => 'This is a very long content that should be truncated to create an excerpt. '.
            'The excerpt should be limited to 150 characters by default. '.
            'This is additional content that will be cut off.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->excerpt)->not->toBeEmpty();
    // Str::limit adds "..." so it can be slightly over 150
    expect(strlen($post->excerpt))->toBeLessThanOrEqual(153);
});

test('post generates excerpt with custom length', function () {
    $user = User::factory()->create();

    $post = new Post([
        'title' => 'Test Post',
        'content' => 'This is a very long content that should be truncated.',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    $excerpt = $post->generateExcerpt($post->content, 50);
    // Str::limit adds "..." so it can be up to 53 characters for a 50 char limit
    expect(strlen($excerpt))->toBeLessThanOrEqual(53);
});

test('post excerpt strips HTML tags', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Test Post',
        'content' => '<p>This is <strong>HTML</strong> content with <em>tags</em>.</p>',
        'status' => PostStatus::DRAFT,
        'author_id' => $user->id,
    ]);

    expect($post->excerpt)->not->toContain('<');
    expect($post->excerpt)->not->toContain('>');
});

test('post published scope only returns published posts', function () {
    $user = User::factory()->create();

    Post::create([
        'title' => 'Published Post',
        'content' => 'Content',
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
        'author_id' => $user->id,
    ]);

    Post::create([
        'title' => 'Draft Post',
        'content' => 'Content',
        'status' => PostStatus::DRAFT,
        'published_at' => null,
        'author_id' => $user->id,
    ]);

    $publishedPosts = Post::published()->get();

    expect($publishedPosts)->toHaveCount(1);
    expect($publishedPosts->first()->title)->toBe('Published Post');
});

test('post sets published_at when status changes to published', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'title' => 'Test Post',
        'content' => 'Content',
        'status' => PostStatus::DRAFT,
        'published_at' => null,
        'author_id' => $user->id,
    ]);

    expect($post->published_at)->toBeNull();

    $post->update([
        'status' => PostStatus::PUBLISHED,
    ]);

    expect($post->fresh()->published_at)->not->toBeNull();
});
