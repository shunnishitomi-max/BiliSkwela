<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $all = ['stock.view', 'stock.in', 'stock.out', 'reports.view', 'users.manage'];
        foreach (['categories', 'suppliers', 'products'] as $resource) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $all[] = $resource.'.'.$action;
            }
        }
        foreach ($all as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        Role::findOrCreate('Admin', 'web')->syncPermissions($all);
        Role::findOrCreate('Manager', 'web')->syncPermissions(array_diff($all, ['users.manage']));
        Role::findOrCreate('Staff', 'web')->syncPermissions(['categories.view', 'suppliers.view', 'products.view', 'stock.view', 'stock.out']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
