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

        'gym-list',
        'gym-create',
        'gym-edit',
        'gym-delete',
        'gym-view',
        'gym-status',
        
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
    
        'plan-list',
        'plan-create',
        'plan-edit',
        'plan-delete',
        'plan-view',
        'plan-status',

        'workout-list',
        'workout-create',
        'workout-edit',
        'workout-delete',
        'workout-view',

        'diet-plan-list',
        'diet-plan-create',
        'diet-plan-edit',
        'diet-plan-delete',
        'diet-plan-view',

        'attendance-list',
        'attendance-create',
        'attendance-edit',
        'attendance-delete',

        'membership-renewal',
        'membership-expired',

        'trainer-list',
        'trainer-create',
        'trainer-edit',
        'trainer-delete',
        'trainer-view',
    ];


    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create Gym user
        $gym = User::create([
            'name' => 'Gym',
            'email' => 'gym@example.com',
            'phone' => '9999999999',
            'password' => Hash::make('password'),
        ]);

        $gym->gym_id = 'GYM' . str_pad($gym->id, 6, '0', STR_PAD_LEFT);
        $gym->save();

        // Create Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleGym = Role::firstOrCreate(['name' => 'Gym']);
        $roleMember = Role::firstOrCreate(['name' => 'Member']);
        $roleMember = Role::firstOrCreate(['name' => 'Trainer']);

        // Permissions for Super Admin
        $adminPermissions = Permission::whereIn('name', [
            'role-list', 'role-create', 'role-edit', 'role-delete', 'role-view',
            'gym-list', 'gym-create', 'gym-edit', 'gym-delete', 'gym-view', 'gym-status',
        ])->pluck('id')->all();

        // Permissions for Gym
        $gymPermissions = Permission::whereIn('name', [
            'user-list', 'user-create', 'user-edit', 'user-delete', 'user-view', 'user-status',
            'assign-plan-list', 'assign-plan-create', 'assign-plan-edit', 'assign-plan-delete', 'assign-plan-view', 'assign-plan-status',
            'plan-list', 'plan-create', 'plan-edit', 'plan-delete', 'plan-view', 'plan-status',
            'workout-list', 'workout-create', 'workout-edit', 'workout-delete', 'workout-view',
            'diet-plan-list','diet-plan-create','diet-plan-edit','diet-plan-delete','diet-plan-view',
            'attendance-list','attendance-create','attendance-edit','attendance-delete',
            'membership-renewal', 'membership-expired','trainer-list','trainer-create','trainer-edit','trainer-delete','trainer-view',
        ])->pluck('id')->all();

        // Assign permissions
        $roleAdmin->syncPermissions($adminPermissions);
        $roleGym->syncPermissions($gymPermissions);

        // Assign roles to users
        $superAdmin->assignRole($roleAdmin);
        $gym->assignRole($roleGym);
    }

}
