<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Dlshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'name',
        'shop_name',
        'email',
        'purpose',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
