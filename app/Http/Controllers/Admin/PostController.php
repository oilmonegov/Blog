<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private TagService $tagService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::with(['author', 'categories', 'tags'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15);

        return view('admin.posts.index', [
            'posts' => $posts,
            'statuses' => PostStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('admin.posts.create', [
            'categories' => Category::orderBy('name')->get(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->input('content'),
            'excerpt' => $request->excerpt,
            'status' => PostStatus::from($request->status),
            'author_id' => $request->user()->id,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Attach categories
        if ($request->filled('categories')) {
            $post->categories()->attach($request->categories);
        }

        // Handle tags (comma-separated string)
        if ($request->filled('tags')) {
            $tagIds = $this->tagService->syncTags($request->tags);
            $post->tags()->attach($tagIds);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['author', 'categories', 'tags', 'comments.user']);

        return view('admin.posts.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $post->load(['categories', 'tags']);

        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $updateData = [
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->input('content'),
            'status' => PostStatus::from($request->status),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];

        // Only update excerpt if provided
        if ($request->filled('excerpt')) {
            $updateData['excerpt'] = $request->excerpt;
        }

        $post->update($updateData);

        // Sync categories
        if ($request->filled('categories')) {
            $post->categories()->sync($request->categories);
        } else {
            $post->categories()->detach();
        }

        // Handle tags (comma-separated string)
        if ($request->filled('tags')) {
            $tagIds = $this->tagService->syncTags($request->tags);
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
