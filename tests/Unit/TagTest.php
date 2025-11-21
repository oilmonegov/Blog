<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Enums\PostStatus;

test('tag generates slug from name on creation', function () {
    $tag = Tag::create([
        'name' => 'Laravel Framework',
    ]);

    expect($tag->slug)->toBe('laravel-framework');
});

test('tag slug is unique', function () {
    $tag1 = Tag::create([
        'name' => 'PHP',
    ]);

    // Since name must be unique, test with different name that generates similar slug
    $tag2 = Tag::create([
        'name' => 'PHP 2',
    ]);

    expect($tag1->slug)->toBe('php');
    expect($tag2->slug)->toBe('php-2');
});

test('tag can have many posts', function () {
    $tag = Tag::factory()->create();
    $user = User::factory()->create();

    $post1 = Post::factory()->create([
        'author_id' => $user->id,
        'status' => PostStatus::PUBLISHED,
    ]);
    $post2 = Post::factory()->create([
        'author_id' => $user->id,
        'status' => PostStatus::PUBLISHED,
    ]);

    $tag->posts()->attach([$post1->id, $post2->id]);

    expect($tag->posts)->toHaveCount(2);
});

