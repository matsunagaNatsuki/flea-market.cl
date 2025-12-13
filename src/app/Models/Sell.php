<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Like;

class Sell extends Model
{
    use HasFactory;

    protected $fillable =['name', 'price', 'description', 'brand',  'image', 'user_id', 'condition_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'sell_category');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sold()
    {
        return Buy::where('sell_id', $this->id)->exists();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function liked(): bool
    {
        $userId = auth()->id();
        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'sell_id')->latest();
    }

    public function getComments()
    {
        $comments = Comment::where('sell_id', $this->id)->get();
        return $comments;
    }

    public function trade()
    {
        return $this->hasOne(Trade::class);
    }

    public function activeTrade()
    {
        return $this->hasOne(Trade::class)->where('status', 'active');
    }


}