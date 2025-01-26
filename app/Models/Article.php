<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Farm;

class Article extends Model
{
    use HasFactory;
    
    protected $keyType = 'string'; // UUIDを文字列として扱う
    public $incrementing = false; // インクリメントIDを無効化

    protected $fillable = [
        'farm_id',
        'title',
        'content',
        'article_images',
        'is_published'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

}
