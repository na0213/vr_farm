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
        'store_name',
        'store_address',
        'store_link',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
