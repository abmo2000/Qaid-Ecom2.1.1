<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'hanyqaid63@gmail.com'],
            [
                'name' => 'Hany Qaid',
                'password' => Hash::make('Root@0100'),
                'role_name'=> 'super_admin',
                'is_approved' => true,
            ]
        );

        // Create a super admin user
        User::query()->updateOrCreate(
            ['email' => 'abdullahmo1882000@gmail.com'],
            [
                'name' => 'Abdullah Mo',
                'password' => Hash::make('Root@0100'),
                'role_name'=> 'super_admin',
                'is_approved' => true,
            ]
        );

        $this->call([
            CitySeeder::class,
        ]);
    }
}
