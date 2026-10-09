<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\PurchasedItem;
use App\Models\Store;
use App\Models\Animal;
use App\Models\FarmImage;
use App\Models\Keyword;
use App\Models\Kind;
use App\Models\Article;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_name',
        'catchcopy',
        'vr',
        'theme',
        'hp_link',
        'has_experience',
        'prefecture',
        'address',
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

    // 公開サイトに出してよい牧場だけに絞る
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // 撮影一覧(牧場ページの GALLERY)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    // 運営者が購入して撮影した商品(登録順)
    public function purchasedItems()
    {
        return $this->hasMany(PurchasedItem::class)->orderBy('id');
    }
    public function stores()
    {
        return $this->hasMany(Store::class);
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
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

}