<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 牧場の登録にオーナーは要らなくなったため、farms.owner_id と owners テーブルを削除する。
     * オーナーはログインにも公開ページにも使われておらず、牧場を作るための入れ物でしかなかった。
     */
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::dropIfExists('owners');
    }

    /**
     * オーナーの情報は元に戻せないため、戻す処理は提供しない。
     */
    public function down(): void
    {
        //
    }
};
