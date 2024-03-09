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

    public function farm()
    {
        return $this->hasOne(Farm::class);
    }
}
