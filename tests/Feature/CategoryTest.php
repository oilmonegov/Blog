<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('admin can view category index', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Category::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));

    $response->assertStatus(200);
    $response->assertViewHas('categories');
});

test('non-admin cannot access category index', function () {
    $user = User::factory()->create(['role' => UserRole::AUTHOR]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertStatus(403);
});

test('admin can create a category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Technology',
        'description' => 'Posts about technology',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'Technology',
        'description' => 'Posts about technology',
    ]);
});

test('category creation requires name', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'description' => 'Posts about technology',
    ]);

    $response->assertSessionHasErrors('name');
});

test('category name must be unique', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    Category::factory()->create(['name' => 'Technology']);

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Technology',
        'description' => 'Another description',
    ]);

    $response->assertSessionHasErrors('name');
});

test('category slug is auto-generated from name', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Web Development',
        'description' => 'Posts about web development',
    ]);

    $category = Category::where('name', 'Web Development')->first();
    expect($category->slug)->toBe('web-development');
});

test('admin can update a category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $category = Category::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
        'name' => 'New Name',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'New Name',
        'description' => 'Updated description',
    ]);
});

test('admin can delete a category', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('non-admin cannot create category', function () {
    $user = User::factory()->create(['role' => UserRole::AUTHOR]);

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'Technology',
    ]);

    $response->assertStatus(403);
});

test('public can view category archive page', function () {
    $category = Category::factory()->create(['slug' => 'technology']);
    $post = Post::factory()->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    $post->categories()->attach($category);

    $response = $this->get(route('categories.show', $category->slug));

    $response->assertStatus(200);
    $response->assertViewHas('category');
    $response->assertViewHas('posts');
    expect($response->viewData('category')->id)->toBe($category->id);
});

test('category archive page shows only published posts', function () {
    $category = Category::factory()->create(['slug' => 'technology']);
    $publishedPost = Post::factory()->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    $draftPost = Post::factory()->create([
        'status' => PostStatus::DRAFT,
    ]);
    $publishedPost->categories()->attach($category);
    $draftPost->categories()->attach($category);

    $response = $this->get(route('categories.show', $category->slug));

    $response->assertStatus(200);
    $posts = $response->viewData('posts');
    expect($posts->count())->toBe(1);
    expect($posts->first()->id)->toBe($publishedPost->id);
});

test('category archive page paginates posts', function () {
    $category = Category::factory()->create(['slug' => 'technology']);
    $posts = Post::factory()->count(15)->create([
        'status' => PostStatus::PUBLISHED,
        'published_at' => now(),
    ]);
    foreach ($posts as $post) {
        $post->categories()->attach($category);
    }

    $response = $this->get(route('categories.show', $category->slug));

    $response->assertStatus(200);
    $posts = $response->viewData('posts');
    expect($posts->count())->toBe(10); // Default pagination
    expect($posts->hasMorePages())->toBeTrue();
});

test('category archive page returns 404 for non-existent category', function () {
    $response = $this->get(route('categories.show', 'non-existent'));

    $response->assertStatus(404);
});

test('category archive page shows posts count in admin index', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $category = Category::factory()->create();
    Post::factory()->count(5)->create()->each(function ($post) use ($category) {
        $post->categories()->attach($category);
    });

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));

    $response->assertStatus(200);
    $categories = $response->viewData('categories');
    $categoryData = $categories->firstWhere('id', $category->id);
    expect($categoryData->posts_count)->toBe(5);
});
