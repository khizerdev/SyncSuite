<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Department;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        // create permissions
        $permissions = [
            'view-department',
            'create-department',
            'edit-department',
            'delete-department',
            
            'view-customer',
            'create-customer',
            'edit-customer',
            'delete-customer',

            'view-vendor',
            'create-vendor',
            'edit-vendor',
            'delete-vendor',

            'view-manufacturer',
            'create-manufacturer',
            'edit-manufacturer',
            'delete-manufacturer',

            'view-particular',
            'create-particular',
            'edit-particular',
            'delete-particular',

            'view-machine',
            'create-machine',
            'edit-machine',
            'delete-machine',

            'view-material',
            'create-material',
            'edit-material',
            'delete-material',

            'view-product-type',
            'create-product-type',
            'edit-product-type',
            'delete-product-type',

            'view-product',
            'create-product',
            'edit-product',
            'delete-product',

            'view-employee',
            'create-employee',
            'edit-employee',
            'delete-employee',

            'view-branch',
            'create-branch',
            'edit-branch',
            'delete-branch',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign created permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo($permissions);

        $user->assignRole('admin');
    }
}
