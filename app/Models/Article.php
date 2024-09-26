<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'title',
        'content',
        'article_images',
        'is_published'
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
