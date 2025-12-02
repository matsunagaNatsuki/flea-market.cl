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

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

}