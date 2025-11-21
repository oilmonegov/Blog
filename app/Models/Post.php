<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'author_id',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the author that owns the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the categories for the post.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Generate slug from title.
     */
    public function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate excerpt from content.
     */
    public function generateExcerpt(string $content, int $length = 150): string
    {
        $excerpt = strip_tags($content);
        $excerpt = Str::limit($excerpt, $length);

        return $excerpt;
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::PUBLISHED)
            ->whereNotNull('published_at');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug) && ! empty($post->title)) {
                $post->slug = $post->generateSlug($post->title);
            }

            if (empty($post->excerpt) && ! empty($post->content)) {
                $post->excerpt = $post->generateExcerpt($post->content);
            }

            if ($post->status === PostStatus::PUBLISHED && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                // Only regenerate slug if it wasn't manually set or if title changed
                // If slug is dirty (manually changed), don't override it
                if (! $post->isDirty('slug')) {
                    $post->slug = $post->generateSlug($post->title);
                }
            }

            if ($post->isDirty('content') && empty($post->excerpt)) {
                $post->excerpt = $post->generateExcerpt($post->content);
            }

            if ($post->isDirty('status') && $post->status === PostStatus::PUBLISHED && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }
}
