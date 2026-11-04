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
        'email_verified_at',
        'email_verification_token',
        'password_hash',
        'role',
        'points',
        'badge_pro',
        'badge_id',
        'badge_vip',
        'status',
        'notify_new_follower',
        'notify_private_message',
        'referral_code',
        'referred_by_user_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notify_new_follower' => 'boolean',
        'notify_private_message' => 'boolean',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
        'email_verification_token',
    ];

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ])->save();
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getBadgeAttribute()
    {
        $badges = [];
        if ($this->badge_vip) $badges[] = 'vip';
        if ($this->badge_id) $badges[] = 'id';
        if ($this->badge_pro) $badges[] = 'pro';
        return implode(' ', $badges);
    }

    public function setBadgeAttribute($value)
    {
        if (empty($value)) {
            $this->badge_pro = false;
            $this->badge_vip = false;
            return;
        }
        $parts = preg_split('/[,\s]+/', strtolower(trim((string)$value)));
        if (in_array('vip', $parts)) {
            $this->badge_vip = true;
            $this->badge_pro = true;
        } elseif (in_array('pro', $parts) || in_array('premium', $parts)) {
            $this->badge_pro = true;
        }
        if (in_array('id', $parts) || in_array('verified', $parts)) {
            $this->badge_id = true;
        }
    }

    public function getBadgesListAttribute()
    {
        $badges = [];
        if ($this->badge_vip) $badges[] = 'vip';
        if ($this->badge_id) $badges[] = 'id';
        if ($this->badge_pro) $badges[] = 'pro';
        return $badges;
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

    public function getActiveSubscriptionAttribute(): ?Donation
    {
        return $this->donations()
            ->whereIn('status', ['confirmed', 'finished'])
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('expires_at')->where('expires_at', '>', now());
                })->orWhere(function($sub) {
                    $sub->whereNull('expires_at')->where('created_at', '>', now()->subDays(365));
                });
            })
            ->latest()
            ->first();
    }

    public function getSubscriptionExpiresAtAttribute(): ?\Carbon\Carbon
    {
        $activeSub = $this->active_subscription;
        if ($activeSub) {
            return $activeSub->expires_at ?: $activeSub->created_at->addDays(365);
        }
        return null;
    }

    public function getPlanAttribute()
    {
        // 1. Check for active, unexpired subscription
        $activeSub = $this->active_subscription;
        if ($activeSub) {
            return $activeSub->plan; // 'premium_plus' or 'premium'
        }

        // 2. Check if user had a confirmed donation that has expired (after 1 year)
        $pastSub = $this->donations()->whereIn('status', ['confirmed', 'finished'])->latest()->first();
        if ($pastSub) {
            $isEnded = ($pastSub->expires_at && $pastSub->expires_at->isPast()) 
                || (!$pastSub->expires_at && $pastSub->created_at->lt(now()->subDays(365)));
            if ($isEnded) {
                // Subscription has ended after 1 year!
                // Clear any subscription-derived badges so user is free to renew/order any plan again
                if ($this->badge_pro || $this->badge_vip) {
                    $this->badge_pro = false;
                    $this->badge_vip = false;
                    $this->save();
                }
                return 'free';
            }
        }

        // 3. Fallback for manually granted admin badges without donation records
        if ($this->badge_vip) {
            return 'premium_plus';
        }
        if ($this->badge_pro) {
            return 'premium';
        }

        return 'free';
    }

    public function getPlanLabelAttribute()
    {
        $plan = $this->plan;
        if ($plan === 'premium_plus') return 'Premium+ ($30)';
        if ($plan === 'premium') return 'Premium ($15)';
        return 'Free';
    }

    public function getLastLoginAttribute()
    {
        return $this->logins()->latest('logged_at')->first();
    }

    public function getReferralEarningsAttribute()
    {
        $count = $this->referrals()->count();
        $pointsPerRef = (int)\App\Models\Setting::get('referral_reward_points', 100);
        return $count * $pointsPerRef;
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id')->latest();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')->latest();
    }

    public function getUnreadMessagesCountAttribute()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    public function prefersFollowerNotification(): bool
    {
        return $this->notify_new_follower !== false && $this->notify_new_follower !== 0;
    }

    public function prefersMessageNotification(): bool
    {
        return $this->notify_private_message !== false && $this->notify_private_message !== 0;
    }
}
