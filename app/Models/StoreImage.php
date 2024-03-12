<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class StoreImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'image',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
