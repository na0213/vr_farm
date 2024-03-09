<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\Keyword;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'farm_name',
        'vr',
        'prefecture',
        'address',
        'kind_id',
        'keyword_id',
        'farm_info',
        'url',
    ];

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function Kind()
    {
        return $this->belongsTo(Kind::class, 'kind_id');
    }
    public function Keyword()
    {
        return $this->belongsTo(Keyword::class, 'keyword_id');
    }

}