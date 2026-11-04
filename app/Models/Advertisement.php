<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'sponsor_name',
        'link_url',
        'image_url',
        'description',
        'placement',
        'is_active',
        'clicks_count',
        'impressions_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
