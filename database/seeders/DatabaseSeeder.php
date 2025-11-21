<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create author users
        $authors = User::factory()->author()->count(5)->create();

        // Create categories
        $categories = Category::factory()->count(10)->create();

        // Create tags
        $tags = Tag::factory()->count(20)->create();

        // Create posts
        $posts = Post::factory()
            ->count(30)
            ->create(function () use ($authors) {
                return [
                    'author_id' => $authors->random()->id,
                ];
            });

        // Publish some posts
        $posts->random(20)->each(function ($post) {
            $post->update([
                'status' => \App\Enums\PostStatus::PUBLISHED,
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        });

        // Attach categories and tags to posts
        $posts->each(function ($post) use ($categories, $tags) {
            $post->categories()->attach($categories->random(rand(1, 3)));
            $post->tags()->attach($tags->random(rand(2, 5)));
        });

        // Create comments
        Comment::factory()
            ->count(50)
            ->create(function () use ($posts, $authors, $admin) {
                $allUsers = $authors->push($admin);

                return [
                    'post_id' => $posts->random()->id,
                    'user_id' => $allUsers->random()->id,
                ];
            });

        // Create test user
        User::factory()->author()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
