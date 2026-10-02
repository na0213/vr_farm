<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // AdminSeeder は管理者の認証情報を含むためローカル専用(.gitignore 対象)。
        // 無い環境では何もしない。管理者は tinker 等で作成する。
        if (class_exists(AdminSeeder::class)) {
            $this->call([AdminSeeder::class]);
        }
    }
}
