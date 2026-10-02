<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'product_name',
        'product_info',
        'product_link',
        'product_image',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    // 公開サイトに出す商品: ECリンクがあり、牧場が公開中のもの
    public function scopeListed($query)
    {
        return $query->whereNotNull('product_link')
            ->where('product_link', '!=', '')
            ->whereHas('farm', fn ($q) => $q->published());
    }
}
