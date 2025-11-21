<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
        $baseSlug = \Illuminate\Support\Str::slug($name);
        
        // Generate a unique slug using the model's logic
        // Check if slug exists and append counter if needed
        $slug = $baseSlug;
        $counter = 1;
        while (Tag::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return [
            'name' => $name,
            'slug' => $slug,
        ];
    }
}
