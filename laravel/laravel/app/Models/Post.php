<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'blog_id',
        'type',
        'status',
        'heading',
        'body',
        'caption',
        'media_path',
        'media_type',
        'media_meta',
        'crop_meta',
        'quote_text',
        'quote_source',
        'link_url',
        'link_title',
        'link_domain',
        'chat_payload',
        'views_count',
        'reblog_of_post_id',
        'published_at',
    ];

    protected $casts = [
        'crop_meta' => 'array',
        'chat_payload' => 'array',
        'published_at' => 'datetime',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function reblogs()
    {
        return $this->hasMany(Post::class, 'reblog_of_post_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }
}
