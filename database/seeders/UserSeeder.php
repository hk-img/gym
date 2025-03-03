<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * List of applications to add.
     */
    private $permissions = [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'role-view',

        'user-list',
        'user-create',
        'user-edit',
        'user-delete',
        'user-view',
        'user-status',

        'assign-plan-list',
        'assign-plan-create',
        'assign-plan-edit',
        'assign-plan-delete',
        'assign-plan-view',
        'assign-plan-status',
        
        'brand-list',
        'brand-create',
        'brand-edit',
        'brand-delete',
        'brand-view',
        'brand-status',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin User and assign the role to him.
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password')
        ]);

        $role = Role::create(['name' => 'Super Admin']);
        $member = Role::create(['name' => 'Member']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
