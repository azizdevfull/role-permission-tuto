<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission1 = Permission::updateOrCreate(['name' => 'admin-get-products']);
        $permission2 = Permission::updateOrCreate(['name' => 'admin-create-products']);
        $permission3 = Permission::updateOrCreate(['name' => 'admin-view-products']);
        $permission4 = Permission::updateOrCreate(['name' => 'admin-update-products']);
        $permission5 = Permission::updateOrCreate(['name' => 'admin-delete-products']);

        $permission6 = Permission::updateOrCreate(['name' => 'admin-get-permissions']);
        $permission7 = Permission::updateOrCreate(['name' => 'admin-get-roles']);
        $permission8 = Permission::updateOrCreate(['name' => 'admin-create-roles']);

        $permission9 = Permission::updateOrCreate(['name' => 'admin-assign-roles']);

        $role = Role::updateOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo([$permission1, $permission2, $permission3, $permission4, $permission5, $permission6, $permission7, $permission8, $permission9]);

        $role2 = Role::updateOrCreate(['name' => 'Admin']);
        $role2->givePermissionTo([$permission1, $permission2, $permission3]);
    }
}
