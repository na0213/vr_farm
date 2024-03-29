<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mypage;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    public function follower()
    {
        // フォローする側
        return $this->belongsTo(Mypage::class, 'follower_id');
    }

    public function followed()
    {
        // フォローされる側
        return $this->belongsTo(Mypage::class, 'followed_id');
    }
}
