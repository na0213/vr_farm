<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // 外部キー制約がある場合は先に削除
            $table->dropForeign(['farm_id']);

            // id を UUID に変更
            $table->uuid('id')->change();

            // 再度 farm_id に外部キーを追加
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // 外部キー制約がある場合は削除
            $table->dropForeign(['farm_id']);

            // id を元の auto-increment に戻す
            $table->bigIncrements('id')->change();

            // 再度 farm_id に外部キーを追加
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }
};
