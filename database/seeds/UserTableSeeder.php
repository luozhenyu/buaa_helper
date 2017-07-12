<?php

use App\Http\Controllers\FileController;
use App\Models\Counsellor;
use App\Models\Department;
use App\Models\DepartmentAdmin;
use App\Models\Student;
use App\Models\SuperAdmin;
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
        $superAdmin = SuperAdmin::create([
            'number' => 10000,
            'name' => '超级管理员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        Auth::login($superAdmin);
        $this->setDepartmentAvatar();

        DepartmentAdmin::create([
            'number' => 10001,
            'name' => '部门管理员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);

        Counsellor::create([
            'number' => 10002,
            'name' => '学院辅导员',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);

        Student::create([
            'number' => 15210001,
            'name' => '学生',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        DB::commit();
    }

    private function setDepartmentAvatar()
    {
        foreach (DepartmentTableSeeder::data as $item) {
            if ($file = FileController::import(Storage::url($item['avatar']))) {
                $department = Department::where('number', $item['number'])->firstOrFail();
                $department->defaultAvatar()->associate($file);
                $department->save();
            } else {
                throw new Exception('图标导入异常,' . $item['avatar']);
            }
        }
    }
}
