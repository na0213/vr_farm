<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'animal_name',
        'animal_info',
        'animal_image',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    public function links()
    {
        return $this->hasMany(AnimalLink::class);
    }
}