<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'trade_messages';

    protected $fillable = [
        'user_id',
        'sell_id',
        'body',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function profile()
    {
        
    }
}
