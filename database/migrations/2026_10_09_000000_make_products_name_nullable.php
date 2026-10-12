<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 撮影一覧(products)の写真は、タイトルなしでも登録できるようにする。
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('products')->whereNull('product_name')->update(['product_name' => '']);

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_name')->nullable(false)->change();
        });
    }
};
