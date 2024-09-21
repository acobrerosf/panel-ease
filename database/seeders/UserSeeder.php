<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin'),
            'type_id' => UserType::SUPER_ADMIN,
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
