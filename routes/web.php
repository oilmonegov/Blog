<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Author\CommentController as AuthorCommentController;
use App\Http\Controllers\Author\PostController as AuthorPostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Public blog routes
Route::get('/', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// Public category and tag archive routes
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{slug}', [TagController::class, 'show'])->name('tags.show');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Comment routes (public, but requires authentication)
    Route::post('/comments', [CommentController::class, 'store'])
        ->middleware('throttle:10,1') // Rate limit: 10 comments per minute
        ->name('comments.store');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', AdminPostController::class);
    Route::patch('posts/{post}/publish', \App\Http\Controllers\Admin\PublishPostController::class)->name('posts.publish');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('comments', AdminCommentController::class)->only(['index', 'destroy']);
});

// Author routes
Route::middleware(['auth', 'author'])->prefix('author')->name('author.')->group(function () {
    Route::resource('posts', AuthorPostController::class);
    Route::patch('posts/{post}/publish', \App\Http\Controllers\Author\PublishPostController::class)->name('posts.publish');
    Route::resource('comments', AuthorCommentController::class)->only(['index', 'destroy']);
});

require __DIR__.'/auth.php';
