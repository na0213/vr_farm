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
        Schema::create('animals', function (Blueprint $table) {
            // 1. ID (UUID)
            $table->uuid('id')->primary();

            // 2. Farm ID (これ1行で「カラム作成」と「紐付け」を両方やります)
            // ※ 他に $table->uuid('farm_id'); と書いている行があれば消してください！
            $table->foreignUuid('farm_id')
                  ->constrained('farms')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // 3. その他のカラム
            $table->string('animal_name')->nullable();
            $table->text('animal_info')->nullable();
            $table->string('animal_image')->nullable();
            
            // 4. 作成日時・更新日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
