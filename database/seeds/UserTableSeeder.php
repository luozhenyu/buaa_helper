<?php

use App\Http\Controllers\FileController;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        $user = User::create([
            'number' => 10000,
            'name' => '超级管理员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(Role::where('name', 'admin')->firstOrFail());
        Auth::login($user);
        $this->setDepartmentAvatar();

        $user = User::create([
            'number' => 10001,
            'name' => '部门管理员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(Role::where('name', 'department.admin')->firstOrFail());

        $user = User::create([
            'number' => 10002,
            'name' => '院系管理员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(Role::where('name', 'college.admin')->firstOrFail());
        DB::commit();
    }

    private function setDepartmentAvatar()
    {
        foreach (DepartmentTableSeeder::data as $item) {
            if ($file = FileController::import(Storage::url($item['avatar']))) {
                $department = Department::where('number', $item['number'])->firstOrFail();
                $department->avatarFile()->associate($file);
                $department->save();
            } else {
                throw new Exception('图标导入异常,' . $item['avatar']);
            }
        }
    }
}
