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
        $adminEmail = 'admin@' . env('SSO_DOMAIN', 'your-domain.com');
        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create a member user for testing
        $memberEmail = 'member@' . env('SSO_DOMAIN', 'your-domain.com');
        $member = User::firstOrCreate(
            ['email' => $memberEmail],
            [
                'name' => 'Member User',
                'password' => Hash::make('member123'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: ' . $adminEmail . ' (password: admin123)');
        $this->command->info('Member user created: ' . $memberEmail . ' (password: member123)');
    }
}