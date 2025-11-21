<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Sync tags from comma-separated string.
     * Creates tags if they don't exist.
     *
     * @param  string  $tagsString
     * @return array<int>
     */
    public function syncTags(string $tagsString): array
    {
        $tagNames = array_map('trim', explode(',', $tagsString));
        $tagNames = array_filter($tagNames);
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }
}

