<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 牧場に紐づかないコラム記事(知識系記事)を作成できるようにする。
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->uuid('farm_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->uuid('farm_id')->nullable(false)->change();
        });
    }
};
