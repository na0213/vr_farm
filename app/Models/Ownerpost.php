<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Ownerpost extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'post_title',
        'post_content',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
