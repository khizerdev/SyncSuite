<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'erp',
            'hr',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'super-admin']);
        $hrRole = Role::create(['name' => 'hr']);
        $erpRole = Role::create(['name' => 'erp']);

        $adminRole->givePermissionTo($permissions); // Admin gets all permissions
        $hrRole->givePermissionTo('hr');
        $erpRole->givePermissionTo('erp');
    }
}
