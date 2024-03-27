<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'point_name',
        'point_info',
        'sdgs',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
