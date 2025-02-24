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
        Schema::create('farms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')
            ->constrained('owners')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('farm_name');
            $table->string('catchcopy')->nullable();
            $table->text('vr')->nullable();
            $table->text('theme')->nullable();
            $table->string('prefecture');
            $table->string('address')->nullable();
            $table->longText('farm_info')->nullable();
            $table->boolean('is_published')->default(true);
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
