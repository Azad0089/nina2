<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCommunityUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['community_id', 'user_id', 'role', 'status', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function community()
    {
        return $this->belongsTo(MemberCommunity::class, 'community_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
