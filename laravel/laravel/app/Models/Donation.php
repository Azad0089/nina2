<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'gateway',
        'order_id',
        'payment_id',
        'amount_usd',
        'plan',
        'status',
        'crypto_currency',
        'crypto_amount',
        'pay_address',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
