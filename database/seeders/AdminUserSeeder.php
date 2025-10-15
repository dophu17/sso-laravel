<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@balocco-local.info'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create a member user for testing
        $member = User::firstOrCreate(
            ['email' => 'member@balocco-local.info'],
            [
                'name' => 'Member User',
                'password' => Hash::make('member123'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@balocco-local.info (password: admin123)');
        $this->command->info('Member user created: member@balocco-local.info (password: member123)');
    }
}