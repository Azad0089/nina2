<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'role',
        'points',
        'badge_pro',
        'badge_id',
        'badge_vip',
        'status',
        'referral_code',
        'referred_by_user_id',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getBadgeAttribute()
    {
        if ($this->badge_vip) return 'vip';
        if ($this->badge_id) return 'id';
        if ($this->badge_pro) return 'pro';
        return '';
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function mainBlog()
    {
        return $this->hasOne(Blog::class)->where('is_main', 1);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }
}
