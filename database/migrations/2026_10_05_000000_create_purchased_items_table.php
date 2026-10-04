<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 運営者が自分で購入して撮影した商品(牧場ごと)。
 * 撮影一覧(products)とは別に持つ。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchased_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('farm_id');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->string('item_name');
            $table->text('item_comment')->nullable(); // 感想(ひとこと)
            $table->string('item_link', 2048)->nullable(); // 販売ページ
            $table->string('item_image'); // 1枚目(パッケージなど。一覧に出る)
            $table->string('item_image_2')->nullable(); // 2枚目(裏面など)
            $table->string('item_image_3')->nullable(); // 3枚目(中身など)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchased_items');
    }
};
