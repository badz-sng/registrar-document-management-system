<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // change this!
            'role' => User::ROLE_ADMIN, // assumes you defined constants in User model
        ]);

        // Example Encoder
        User::create([
            'name' => 'Encoder User',
            'email' => 'encoder@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ENCODER,
        ]);

        // Example Processor
        User::create([
            'name' => 'Processor User',
            'email' => 'processor@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_PROCESSOR,
        ]);

        // Example Verifier
        User::create([
            'name' => 'Verifier User',
            'email' => 'verifier@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_VERIFIER,
        ]);

        // Example Retriever
        User::create([
            'name' => 'Retriever User',
            'email' => 'retriever@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_RETRIEVER,
        ]);
    }
}
