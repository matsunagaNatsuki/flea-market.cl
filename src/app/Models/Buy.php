<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sell;
use App\Models\User;

class Buy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sell_id',
        'buy_postal_code',
        'buy_address',
        'buy_building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'sell_id');
    }

    public function sold()
    {
        return Buy::where('sell_id', $this->id)->exists();
    }
}
