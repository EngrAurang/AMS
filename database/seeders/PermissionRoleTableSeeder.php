<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));
        // $user_permissions = $admin_permissions->filter(function ($permission) {
        //     return substr($permission->title, 0, 5) != 'user_' && substr($permission->title, 0, 5) != 'role_' && substr($permission->title, 0, 11) != 'permission_';
        // });
        // Role::findOrFail(2)->permissions()->sync($user_permissions);
        $employee_permissions = Permission::whereIn( 'id', [ 22, 23,24,25,26,27] )->pluck( 'id' )->toArray();
        Role::findOrFail( 2 )->permissions()->sync( $employee_permissions );
        $line_manager_permissions = Permission::whereIn( 'id', [ 22, 23,24,25,26,27] )->pluck( 'id' )->toArray();
        Role::findOrFail( 3 )->permissions()->sync( $line_manager_permissions );
    }
}