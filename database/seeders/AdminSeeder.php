<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'name' => 'NATSUMI',
            'email' => 'fooch.info@gmail.com',
            'password' => Hash::make('Natsu0723'),
            'created_at' => '2024-03-09 02:36:33',
            'updated_at' => '2024-03-09 02:36:33'
        ]);
    }
}
