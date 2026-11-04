<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'blog_id',
        'type',
        'status',
        'is_community',
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
        'is_pinned',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'media_meta' => 'array',
        'crop_meta' => 'array',
        'chat_payload' => 'array',
        'published_at' => 'datetime',
    ];

    public function getTagsListAttribute()
    {
        $tags = [];
        if (!empty($this->media_meta) && is_array($this->media_meta) && !empty($this->media_meta['tags'])) {
            $tags = (array)$this->media_meta['tags'];
        }
        $text = ($this->heading ?? '') . ' ' . ($this->caption ?? '') . ' ' . ($this->body ?? '') . ' ' . ($this->quote_text ?? '');
        preg_match_all('/#([a-zA-Z0-9_\-]+)/u', $text, $matches);
        if (!empty($matches[1])) {
            $tags = array_merge($tags, $matches[1]);
        }
        return array_values(array_unique(array_filter(array_map(function($t) {
            return strtolower(trim(ltrim((string)$t, '#')));
        }, $tags))));
    }

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

    public function getTagsAttribute()
    {
        return $this->tags_list;
    }
}
