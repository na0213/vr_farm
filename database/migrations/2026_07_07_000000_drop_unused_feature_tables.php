<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * オーナー・マスター・ショップ・ユーザー向け機能とコミュニティ機能の廃止に伴い、
     * 使われなくなったテーブルを削除する。
     * histories / points / qrs / dlshops / ownerposts はマイグレーション未管理のため
     * 環境によって存在しない可能性があり、dropIfExists で安全に削除する。
     */
    public function up(): void
    {
        // 外部キー制約の依存先より先に子テーブルを削除する
        $tables = [
            'likes',
            'follows',
            'posts',
            'ownerposts',
            'notes',
            'mypages',
            'histories',
            'points',
            'qrs',
            'dlshops',
            'animal_links',
            'masters',
            'master_password_reset_tokens',
            'shops',
            'shop_password_reset_tokens',
            'owner_password_reset_tokens',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * 機能ごと廃止のため元に戻す処理は提供しない。
     */
    public function down(): void
    {
        //
    }
};
