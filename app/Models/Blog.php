<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'tagline',
        'color',
        'avatar_path',
        'cover_path',
        'is_main',
        'status',
        'searchable',
        'show_profile',
        'bio',
        'website1',
        'website2',
        'website3',
        'contact_email',
        'is_featured',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBadgeAttribute()
    {
        return $this->user ? $this->user->badge : '';
    }

    public function getCoverPathAttribute($value)
    {
        if ($value === 'hero.jpg' || $value === 'assets/hero.jpg' || $value === '/assets/hero.jpg') {
            return null;
        }
        return $value;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class);
    }

    /**
     * Get featured blogs for Explore and Trending display.
     * Respects max count setting, pins admin blog(s), and rotates advertising/featured blogs evenly.
     */
    public static function getFeaturedForDisplay(?int $limit = null)
    {
        try {
            Promotion::expireOverdue();
        } catch (\Throwable $e) {}

        if ($limit === null) {
            $limit = (int)Setting::get('featured_blogs_max_count', 6);
        }
        if ($limit <= 0) {
            $limit = 6;
        }

        $promotedBlogIds = [];
        try {
            $promotedBlogIds = Promotion::where('status', 'active')
                ->whereNotNull('blog_id')
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->pluck('blog_id')
                ->unique()
                ->toArray();
        } catch (\Throwable $e) {}

        $candidates = self::where('status', 'active')
            ->where(function($q) use ($promotedBlogIds) {
                $q->where('is_featured', 1);
                if (!empty($promotedBlogIds)) {
                    $q->orWhereIn('id', $promotedBlogIds);
                }
            })
            ->with(['user'])
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        // Separate Admin Blog(s) so they stay pinned and are never rotated out
        $adminBlogs = $candidates->filter(function($b) {
            return ($b->user && $b->user->role === 'admin') || $b->slug === 'admin';
        });

        // Other featured / promoted blogs that bought placement or were set as featured
        $otherBlogs = $candidates->reject(function($b) {
            return ($b->user && $b->user->role === 'admin') || $b->slug === 'admin';
        });

        $remainingSlots = max(0, $limit - $adminBlogs->count());

        if ($otherBlogs->count() <= $remainingSlots) {
            $selectedOthers = $otherBlogs;
        } else {
            // Even rotation: shuffle each request so all promoted blogs get fair, equal impressions
            $selectedOthers = $otherBlogs->shuffle()->take($remainingSlots);
        }

        return $adminBlogs->concat($selectedOthers)->values();
    }
}
