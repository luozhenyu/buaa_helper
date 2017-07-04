<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionAndRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $create_user = Permission::create([
            'name' => 'create_user',
            'display_name' => '创建用户',
            'description' => '',
        ]);

        $delete_user = Permission::create([
            'name' => 'delete_user',
            'display_name' => '删除用户',
            'description' => '',
        ]);

        $modify_all_user = Permission::create([
            'name' => 'modify_all_user',
            'display_name' => '修改所有用户信息',
            'description' => '',
        ]);

        $modify_owned_user = Permission::create([
            'name' => 'modify_owned_user',
            'display_name' => '修改隶属用户信息',
            'description' => '',
        ]);

        $view_all_user = Permission::create([
            'name' => 'view_all_user',
            'display_name' => '查看所有用户',
            'description' => '',
        ]);

        $view_owned_user = Permission::create([
            'name' => 'view_owned_user',
            'display_name' => '查看从属用户',
            'description' => '',
        ]);


        $create_notification = Permission::create([
            'name' => 'create_notification',
            'display_name' => '新增通知',
            'description' => '',
        ]);

        $delete_notification = Permission::create([
            'name' => 'delete_notification',
            'display_name' => '删除通知',
            'description' => '',
        ]);

        $delete_owned_notification = Permission::create([
            'name' => 'delete_owned_notification',
            'display_name' => '删除从属通知',
            'description' => '',
        ]);

        $modify_all_notification = Permission::create([
            'name' => 'modify_all_notification',
            'display_name' => '编辑所有通知',
            'description' => '',
        ]);

        $modify_owned_notification = Permission::create([
            'name' => 'modify_owned_notification',
            'display_name' => '编辑从属通知',
            'description' => '',
        ]);


        $superAdmin = Role::create([
            'name' => 'superAdmin',
            'display_name' => '超级管理员',
            'description' => '',
        ]);
        $superAdmin->attachPermission([
            $create_user, $delete_user, $modify_all_user, $view_all_user,
            $create_notification, $delete_notification, $modify_all_notification
        ]);

        $representative = Role::create([
            'name' => 'representative',
            'display_name' => '客服代表',
            'description' => '',
        ]);
        $representative->attachPermission([
            $view_all_user, $modify_all_user,
            $create_notification, $delete_notification, $modify_all_notification
        ]);

        $departmentAdmin = Role::create([
            'name' => 'departmentAdmin',
            'display_name' => '部门管理员',
            'description' => '',
        ]);
        $departmentAdmin->attachPermission([
            $view_all_user,
            $create_notification, $modify_owned_notification
        ]);

        $counsellor = Role::create([
            'name' => 'counsellor',
            'display_name' => '学院辅导员',
            'description' => '',
        ]);
        $counsellor->attachPermission([
            $view_owned_user, $modify_owned_user,
            $create_notification, $modify_owned_notification
        ]);

        $student = Role::create([
            'name' => 'student',
            'display_name' => '学生用户',
            'description' => '',
        ]);
    }
}
