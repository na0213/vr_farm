<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\FarmStore;
use App\Models\Animal;
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
        'catchcopy',
        'vr',
        'theme',
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
    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
    public function kinds()
    {
        return $this->belongsToMany(Kind::class, 'farm_kind', 'farm_id', 'kind_id');
    }
    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'farm_keyword', 'farm_id', 'keyword_id');
    }
    public function farmImages()
    {
        return $this->hasMany(FarmImage::class);
    }
    public function storeimages()
    {
        return $this->hasMany(StoreImage::class);
    }
}