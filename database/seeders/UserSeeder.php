<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Abel Cobreros',
            'email' => 'info@abelcobreros.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin'),
            'type_id' => UserType::FULL_ADMINISTRATOR,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (app()->isLocal()) {
            User::factory()
                ->count(10)
                ->create();
        }
    }
}
