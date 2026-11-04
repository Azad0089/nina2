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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class);
    }
}
