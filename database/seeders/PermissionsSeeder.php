<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected array $permissions = Permission::PERMISSIONS;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPermissions();
    }

    public function createPermissions(): PermissionsSeeder
    {
        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, Permission::GUARD);
        }
        return $this;
    }
}
