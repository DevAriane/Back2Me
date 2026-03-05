<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'surveillant@gmail.com'],
            [
                'name' => 'Surveillent General',
                'password' => Hash::make('surveillant123'),
                'role' => 'admin',
            ]
        );
    }
}
