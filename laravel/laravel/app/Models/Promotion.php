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
