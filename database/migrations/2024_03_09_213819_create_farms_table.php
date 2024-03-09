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
            $table->id();
            $table->foreignId('owner_id')
            ->constrained('owners')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('farm_name');
            $table->string('vr')->nullable();
            $table->string('prefecture');
            $table->string('address');
            $table->foreignId('kind_id')
            ->constrained('kinds', 'id')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('keyword_id')
            ->constrained('keywords', 'id')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->text('farm_info')->nullable();
            $table->text('url')->nullable();
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
