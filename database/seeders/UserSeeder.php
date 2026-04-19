<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'Daksh',
            'email' => 'Daksh@gmail.com',
            'password' => Hash::make('123456'),
            'phone' => '+1234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional admin users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin users created successfully!');
    }
}
