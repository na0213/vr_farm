<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'store_name',
        'store_url',
    ];
}