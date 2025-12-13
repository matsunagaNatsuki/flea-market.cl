<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'sell_id',
        'buyer_profile_id',
        'status',
    ];

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function buyerProfile()
    {
        return $this->belongsTo(Profile::class, 'buyer_profile_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)
                    ->orderBy('created_at', 'desc');
    }
}
