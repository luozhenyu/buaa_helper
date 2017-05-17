<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentTableSeeder::class);
        $this->call(PermissionAndRoleSeeder::class);
        $this->call(UserTableSeeder::class);

        factory(\App\Models\User::class, 100)->create();
        factory(\App\Models\Notification::class, 100)->create();
    }
}
