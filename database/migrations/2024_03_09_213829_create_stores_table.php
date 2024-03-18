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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')
            ->constrained('farms')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_info')->nullable();
            $table->string('product_link')->nullable();
            $table->string('product_image')->nullable();
            $table->datetime('created_at');
            $table->datetime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
