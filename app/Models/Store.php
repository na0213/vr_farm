<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'product_name',
        'product_info',
        'product_image',
    ];

    public function farm()
    {
        return $this->hasOne(Farm::class);
    }
}