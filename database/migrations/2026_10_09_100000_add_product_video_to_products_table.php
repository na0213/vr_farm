<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 撮影一覧(products)に短い動画を登録できるようにする。
     * 動画の行も product_image(動画の最初の場面)を持つので、一覧はこれまでどおり写真で並ぶ。
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_video')->nullable()->after('product_image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_video');
        });
    }
};
