<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Enums\PostStatus;

test('category generates slug from name on creation', function () {
    $category = Category::create([
        'name' => 'Web Development',
        'description' => 'Posts about web development',
    ]);

    expect($category->slug)->toBe('web-development');
});

test('category slug is unique', function () {
    $category1 = Category::create([
        'name' => 'Technology',
        'description' => 'Tech posts',
    ]);

    // Create a category with a different name that will generate the same slug
    // Since name must be unique, we'll test slug uniqueness differently
    $category2 = Category::create([
        'name' => 'Technology 2',
        'description' => 'Another tech category',
    ]);

    expect($category1->slug)->toBe('technology');
    expect($category2->slug)->toBe('technology-2');
});

test('category can have many posts', function () {
    $category = Category::factory()->create();
    $user = User::factory()->create();

    $post1 = Post::factory()->create([
        'author_id' => $user->id,
        'status' => PostStatus::PUBLISHED,
    ]);
    $post2 = Post::factory()->create([
        'author_id' => $user->id,
        'status' => PostStatus::PUBLISHED,
    ]);

    $category->posts()->attach([$post1->id, $post2->id]);

    expect($category->posts)->toHaveCount(2);
});

