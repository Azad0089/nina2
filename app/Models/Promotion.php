<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'blog_id',
        'package_name',
        'placement',
        'points_spent',
        'target_views',
        'current_views',
        'duration_days',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static function expireOverdue(): void
    {
        // 1. Time-based: expires_at is past
        self::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'completed']);

        // 2. Duration days fallback if expires_at is null
        self::where('status', 'active')
            ->whereNull('expires_at')
            ->where('duration_days', '>', 0)
            ->whereNotNull('starts_at')
            ->whereRaw('DATE_ADD(starts_at, INTERVAL duration_days DAY) <= NOW()')
            ->update(['status' => 'completed']);

        // 3. CPM Views: current_views >= target_views
        self::where('status', 'active')
            ->where('target_views', '>', 0)
            ->whereColumn('current_views', '>=', 'target_views')
            ->update(['status' => 'completed']);
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return true;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }
        if ($this->duration_days > 0 && $this->starts_at && $this->starts_at->copy()->addDays($this->duration_days)->isPast()) {
            return true;
        }
        if ($this->target_views > 0 && ($this->current_views ?? 0) >= $this->target_views) {
            return true;
        }
        return false;
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->is_expired) {
            return 'completed';
        }
        return $this->status;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
