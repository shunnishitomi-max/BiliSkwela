<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RolesAndPermissionsSeeder extends Seeder {
    public function run(): void {
        foreach (['Admin','Manager','Staff'] as $role) Role::findOrCreate($role,'web');
    }
}
