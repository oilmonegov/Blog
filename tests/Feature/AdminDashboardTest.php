<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->author = User::factory()->create(['role' => UserRole::AUTHOR]);
});

test('admin can access admin dashboard', function () {
    actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin Dashboard')
        ->assertSee('Total Posts')
        ->assertSee('Published')
        ->assertSee('Drafts')
        ->assertSee('Categories')
        ->assertSee('Total Comments')
        ->assertSee('Total Users');
});

test('author cannot access admin dashboard', function () {
    actingAs($this->author)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('guest cannot access admin dashboard', function () {
    get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('admin dashboard displays correct statistics', function () {
    // Create test data
    Post::factory()->count(5)->create(['status' => PostStatus::PUBLISHED]);
    Post::factory()->count(3)->create(['status' => PostStatus::DRAFT]);
    Category::factory()->count(4)->create();
    Comment::factory()->count(7)->create();
    User::factory()->count(2)->create();

    actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('8') // Total posts (5 published + 3 drafts)
        ->assertSee('5') // Published posts
        ->assertSee('3') // Draft posts
        ->assertSee('4') // Categories
        ->assertSee('7') // Comments
        ->assertSee('4'); // Users (2 created + admin + author)
});

test('admin dashboard displays recent posts', function () {
    $recentPost = Post::factory()->create([
        'title' => 'Recent Post',
        'author_id' => $this->admin->id,
        'status' => PostStatus::PUBLISHED,
    ]);

    actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Recent Post')
        ->assertSee('Recent Posts');
});

test('admin dashboard displays recent comments', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'content' => 'Test comment content',
    ]);

    actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Recent Comments')
        ->assertSee('Test comment content');
});

test('admin can access admin posts index', function () {
    actingAs($this->admin)
        ->get(route('admin.posts.index'))
        ->assertOk()
        ->assertSee('Post Management');
});

test('admin can access admin categories index', function () {
    actingAs($this->admin)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Category Management');
});

test('admin can access admin comments index', function () {
    actingAs($this->admin)
        ->get(route('admin.comments.index'))
        ->assertOk()
        ->assertSee('Comment Management');
});

test('author cannot access admin posts index', function () {
    actingAs($this->author)
        ->get(route('admin.posts.index'))
        ->assertForbidden();
});

test('author cannot access admin categories index', function () {
    actingAs($this->author)
        ->get(route('admin.categories.index'))
        ->assertForbidden();
});

test('author cannot access admin comments index', function () {
    actingAs($this->author)
        ->get(route('admin.comments.index'))
        ->assertForbidden();
});

