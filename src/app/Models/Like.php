<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'sell_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }
}
