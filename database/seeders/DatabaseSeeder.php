<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Day;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Days - Safely
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            Day::firstOrCreate(['day_name' => $day]);
        }

        // 2. Create Admin Account - Safely
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], 
            [
                'name' => 'Admin',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
            ]
        );
    }
}