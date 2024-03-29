<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\Follow;

class Mypage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'catchphrase',
        'my_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function following()
    {
        // フォローしているユーザー
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followers()
    {
        // フォローされているユーザー
        return $this->hasMany(Follow::class, 'followed_id');
    }


}
