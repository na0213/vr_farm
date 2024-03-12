<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Kind extends Model
{
    use HasFactory;

    protected $fillable = [
        'kind',
    ];

    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'farm_kind');
    }
}
