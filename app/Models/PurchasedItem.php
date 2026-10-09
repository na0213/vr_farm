<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 運営者が自分で購入して撮影した商品。牧場ページの「買ってみた」と、お取り寄せページに出す。
 * 写真は3枚まで(1枚目=パッケージなど・必須、2枚目=裏面など、3枚目=中身など)。
 */
class PurchasedItem extends Model
{
    // 写真の欄(並び順どおり)
    public const IMAGE_FIELDS = ['item_image', 'item_image_2', 'item_image_3'];

    protected $fillable = [
        'farm_id',
        'item_name',
        'item_comment',
        'item_link',
        'item_image',
        'item_image_2',
        'item_image_3',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    // 公開サイトに出す商品: 牧場が公開中のもの
    public function scopeListed($query)
    {
        return $query->whereHas('farm', fn ($q) => $q->published());
    }

    /**
     * 登録されている写真のURL(空の欄は詰める)。
     *
     * @return list<string>
     */
    public function images(): array
    {
        return array_values(array_filter(array_map(fn ($field) => $this->{$field}, self::IMAGE_FIELDS)));
    }
}
