<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\FarmStore;
use App\Models\FarmImage;
use App\Models\StoreImage;
use App\Models\Keyword;
use App\Models\Kind;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'farm_name',
        'vr',
        'prefecture',
        'address',
        'farm_info',
    ];

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function stores()
    {
        return $this->hasMany(FarmStore::class);
    }
    public function kinds()
    {
        return $this->belongsToMany(Kind::class, 'farm_kind');
    }
    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'farm_keyword');
    }
    public function images()
    {
        return $this->hasMany(FarmImage::class);
    }
    public function storeimages()
    {
        return $this->hasMany(StoreImage::class);
    }
}