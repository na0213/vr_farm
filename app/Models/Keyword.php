<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
    ];

    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'farm_keyword');
    }
}
