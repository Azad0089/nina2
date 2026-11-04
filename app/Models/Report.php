<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'reason',
        'details',
        'status',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getPostAttribute()
    {
        if ($this->target_type === 'post' || is_numeric($this->target_id)) {
            return Post::with(['blog.user'])->find($this->target_id);
        }
        return null;
    }

    public function getBlogAttribute()
    {
        if ($this->target_type === 'blog') {
            return Blog::find($this->target_id) ?: Blog::where('slug', $this->target_id)->first();
        }
        return null;
    }
}
