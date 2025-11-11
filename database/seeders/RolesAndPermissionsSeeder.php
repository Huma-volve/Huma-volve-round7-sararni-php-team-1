<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions

        // Customer permissions
        $customerPermissions = [
            'view_tours',
            'view_flights',
            'view_cars',
            'view_hotels',
            'create_booking',
            'view_own_bookings',
            'cancel_own_booking',
            'create_review',
            'view_reviews',
            'manage_favorites',
            'manage_own_profile',
        ];

        // Admin permissions
        $adminPermissions = [
            'manage_categories',
            'manage_tours',
            'manage_flights',
            'manage_cars',
            'manage_hotels',
            'manage_all_bookings',
            'update_booking_status',
            'process_refunds',
            'moderate_reviews',
            'view_reports',
            'view_users',
            'update_users',
        ];

        // Super Admin additional permissions
        $superAdminPermissions = [
            'manage_roles_permissions',
            'manage_all_users',
            'manage_system_settings',
        ];

        // Support Agent permissions
        $supportAgentPermissions = [
            'view_bookings',
            'update_booking_status',
            'view_user_queries',
            'manage_user_queries',
        ];

        // Support Manager additional permissions
        $supportManagerPermissions = [
            'manage_support_team',
            'view_support_reports',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $customerPermissions,
            $adminPermissions,
            $superAdminPermissions,
            $supportAgentPermissions,
            $supportManagerPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Customer role
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions($customerPermissions);

        // Admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(array_merge($adminPermissions, $customerPermissions));

        // Super Admin role (has all admin permissions + additional)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions(array_merge(
            $customerPermissions,
            $adminPermissions,
            $superAdminPermissions
        ));

        // Support Agent role
        $supportAgentRole = Role::firstOrCreate(['name' => 'support_agent']);
        $supportAgentRole->syncPermissions(array_merge(
            $supportAgentPermissions,
            ['view_reviews'] // Support can view reviews
        ));

        // Support Manager role (has all support agent permissions + additional)
        $supportManagerRole = Role::firstOrCreate(['name' => 'support_manager']);
        $supportManagerRole->syncPermissions(array_merge(
            $supportAgentPermissions,
            $supportManagerPermissions,
            ['view_reviews']
        ));
    }
}
