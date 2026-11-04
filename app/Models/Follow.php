<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'blog_id', 'created_at'];

    public function user() { return $this->belongsTo(User::class); }
    public function blog() { return $this->belongsTo(Blog::class); }
}
