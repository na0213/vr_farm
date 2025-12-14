<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;
use Illuminate\Support\Str;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'animal_name',
        'animal_info',
        'animal_image',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        // 新規作成時にUUIDを生成
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
    
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    public function links()
    {
        return $this->hasMany(AnimalLink::class);
    }
}