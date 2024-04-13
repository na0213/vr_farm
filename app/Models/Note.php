<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'mypage_id',
        'note_title',
        'note_content',
        'note_image',
    ];

    public function mypage()
    {
        return $this->belongsTo(Mypage::class);
    }
}
