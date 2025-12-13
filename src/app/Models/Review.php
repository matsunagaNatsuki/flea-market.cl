<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'trade_id',
        'from_user_id',
        'to_user_id',
        'score',
    ];

    /**
     * 評価した側のプロフィール
     */
    public function fromProfile()
    {
        return $this->belongsTo(Profile::class, 'from_user_id');
    }

    /**
     * 評価された側のプロフィール
     */
    public function toProfile()
    {
        return $this->belongsTo(Profile::class, 'to_user_id');
    }

}
