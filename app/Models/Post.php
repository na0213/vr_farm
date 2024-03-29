<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mypage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'mypage_id',
        'post_title',
        'post_content',
    ];

    public function mypage()
    {
        return $this->belongsTo(Mypage::class);
    }
}
