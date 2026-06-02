<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Day;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // 1. Seed Days
       $day_seed = [
            ['id'=>'1','day_name'=>'Monday',],
            ['id'=>'2','day_name'=>'Tuesday'],
            ['id'=>'3','day_name'=>'Wednesday'],
            ['id'=>'4','day_name'=>'Thursday'],
            ['id'=>'5','day_name'=>'Friday'],
            ['id'=>'6','day_name'=>'Saturday'],
            ['id'=>'7','day_name'=>'Sunday'],
            ];

        foreach ($day_seed as $day_seed)
        {
            Day::firstOrCreate($day_seed);
        }

        // 2. Create Admin Account
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Password123!'), // Always hash your passwords!
            'role' => 'admin', // Make sure your User model has 'role' in $fillable
        ]);
    }
}
