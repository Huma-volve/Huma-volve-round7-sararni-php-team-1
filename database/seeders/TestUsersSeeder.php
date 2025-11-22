<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testUsers = [
            [
                'name' => 'Customer User',
                'email' => 'customer@test.com',
                'password' => 'password123',
                'role' => 'customer',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => 'password123',
                'role' => 'admin',
            ],
            [
                'name' => 'Super Admin User',
                'email' => 'superadmin@test.com',
                'password' => 'password123',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Support Agent',
                'email' => 'support@test.com',
                'password' => 'password123',
                'role' => 'support_agent',
            ],
            [
                'name' => 'Support Manager',
                'email' => 'support.manager@test.com',
                'password' => 'password123',
                'role' => 'support_manager',
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            // تحديث البيانات إذا كان المستخدم موجود بالفعل
            if (! $user->wasRecentlyCreated) {
                $user->update([
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]);
            }

            // إزالة جميع الأدوار الحالية وتعيين الدور الجديد
            $user->syncRoles([$userData['role']]);

            $this->command->info("✓ {$userData['role']}: {$userData['email']} / {$userData['password']}");
        }

        $this->command->newLine();
        $this->command->info('All test users created successfully!');
        $this->command->info('Password for all users: password123');
    }
}

