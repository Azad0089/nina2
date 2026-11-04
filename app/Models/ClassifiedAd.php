<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassifiedAd extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'tags', 'price', 'contact_email',
        'phone', 'location', 'image_one', 'image_two', 'status', 'expires_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
