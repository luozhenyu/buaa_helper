<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'number' => 10000,
            'name' => 'admin',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(\App\Models\Role::where('name', 'admin')->firstOrFail());

        $user = User::create([
            'number' => 10001,
            'name' => 'admin',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(\App\Models\Role::where('name', 'department.admin')->firstOrFail());

        $user = User::create([
            'number' => 10002,
            'name' => 'admin',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(\App\Models\Role::where('name', 'college.admin')->firstOrFail());

        $user = User::create([
            'number' => 15211121,
            'name' => '罗震宇',
            'password' => bcrypt('123456'),
            'department_id' => 21,
        ]);
        $user->attachRole(\App\Models\Role::where('name', 'department.admin')->firstOrFail());
    }
}
