<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Store;
use App\Models\Animal;
use App\Models\FarmImage;
use App\Models\StoreImage;
use App\Models\Point;
use App\Models\Keyword;
use App\Models\Kind;
use App\Models\Qr;
use App\Models\Ownerpost;
use App\Models\Dlshop;
use App\Models\Article;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'farm_name',
        'catchcopy',
        'vr',
        'theme',
        'hp_link',
        'has_experience',
        'prefecture',
        'address',
        'farm_info',
        'instagram_link',
    ];

    // UUIDの使用を宣言
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
        parent::boot();

        // 新規作成時にUUIDを生成
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function ownerposts()
    {
        return $this->hasMany(Ownerpost::class);
    }
    public function points()
    {
        return $this->hasMany(Point::class);
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
    public function isFavoriteBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    public function qr()
    {
        return $this->hasOne(Qr::class);
    }
    public function dlshops()
    {
        return $this->hasMany(Dlshop::class);
    }
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

}