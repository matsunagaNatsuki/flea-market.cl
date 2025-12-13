<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Profile extends Model
{

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'postal_code',
        'address',
        'building',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTimestamps();
    }

    // 他の人を評価したレビュー
    public function sentReviews()
    {
        return $this->hasMany(Review::class, 'from_user_id');
    }

    // 自分が受け取ったレビュー
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'to_user_id');
    }

    public function trades()
    {
        return $this->hasMany(Trade::class, 'buyer_profile_id', );
    }
}