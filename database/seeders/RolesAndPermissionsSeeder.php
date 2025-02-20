<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Misc
        $miscPermission = Permission::create(['name' => 'N/A']);

        // USER MODEL
        $userPermission1 = Permission::create(['name' => 'create:user']);
        $userPermission2 = Permission::create(['name' => 'read:user']);
        $userPermission3 = Permission::create(['name' => 'update:user']);
        $userPermission4 = Permission::create(['name' => 'delete:user']);

        // ROLE MODEL
        $rolePermission1 = Permission::create(['name' => 'create:role']);
        $rolePermission2 = Permission::create(['name' => 'read:role']);
        $rolePermission3 = Permission::create(['name' => 'update:role']);
        $rolePermission4 = Permission::create(['name' => 'delete:role']);

        // PERMISSION MODEL
        $permission1 = Permission::create(['name' => 'create:permission']);
        $permission2 = Permission::create(['name' => 'read:permission']);
        $permission3 = Permission::create(['name' => 'update:permission']);
        $permission4 = Permission::create(['name' => 'delete:permission']);

        // ADMINS
        $adminPermission1 = Permission::create(['name' => 'read:admin']);
        $adminPermission2 = Permission::create(['name' => 'update:admin']);

        // CREATE ROLES
        $userRole = Role::create(['name' => 'user'])->syncPermissions([
            $miscPermission,
        ]);

        $superAdminRole = Role::create(['name' => 'superman'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
            $userPermission1,
        ]);

        $adminRole = Role::create(['name' => 'admin'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
            $userPermission1,
        ]);

        $moderatorRole = Role::create(['name' => 'moderator'])->syncPermissions([
            $userPermission2,
            $rolePermission2,
            $permission2,
            $adminPermission1,
        ]);

        $developerRole = Role::create(['name' => 'developer'])->syncPermissions([
            $adminPermission1,
        ]);

        // CREATE ADMINS & USERS
        User::create([
            'name' => 'super admin',
            'is_admin' => 1,
            'email' => 'masbay@lulabyte.id',
            'email_verified_at' => now(),
            'password' => Hash::make('qwpo'),
            'remember_token' => Str::random(10),
        ])->assignRole($superAdminRole);

        User::create([
            'name' => 'admin',
            'is_admin' => 1,
            'email' => 'adm@lulabyte.id',
            'email_verified_at' => now(),
            'password' => Hash::make('qwpo'),
            'remember_token' => Str::random(10),
        ])->assignRole($adminRole);

        User::create([
            'name' => 'moderator',
            'is_admin' => 1,
            'email' => 'mod@lulabyte.id',
            'email_verified_at' => now(),
            'password' => Hash::make('qwpo'),
            'remember_token' => Str::random(10),
        ])->assignRole($moderatorRole);

        User::create([
            'name' => 'developer',
            'is_admin' => 1,
            'email' => 'dev@lulabyte.id',
            'email_verified_at' => now(),
            'password' => Hash::make('qwpo'),
            'remember_token' => Str::random(10),
        ])->assignRole($developerRole);

        for ($i=1; $i < 3; $i++) {
            User::create([
                'name' => 'Test '.$i,
                'is_admin' => 0,
                'email' => 'test'.$i.'@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // password
                'remember_token' => Str::random(10),
            ])->assignRole($userRole);
        }
    }
}
