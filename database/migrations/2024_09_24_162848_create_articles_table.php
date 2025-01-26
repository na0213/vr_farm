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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->uuid('farm_id');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->string('title'); // 記事のタイトル
            $table->longText('article_content'); // 記事の内容
            $table->json('article_images')->nullable();
            $table->boolean('is_published')->default(true);
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->softDeletes(); // ソフトデリート機能
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
