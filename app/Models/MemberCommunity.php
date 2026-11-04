<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCommunity extends Model
{
    protected $fillable = [
        'owner_id', 'slug', 'title', 'tagline', 'bio', 'color',
        'avatar_path', 'cover_path', 'website1', 'website2', 'website3',
        'contact_email', 'join_mode', 'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function memberships()
    {
        return $this->hasMany(MemberCommunityUser::class, 'community_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'member_community_id');
    }

    public function activeMembers()
    {
        return $this->memberships()->where('status', 'active');
    }
}
