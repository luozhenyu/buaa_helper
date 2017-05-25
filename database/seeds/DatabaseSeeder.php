<?php

use App\Models\Role;
use App\Models\User;
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

        $normal = Role::where('name', 'normal')->firstOrFail();
        factory(User::class, 100)->create()->each(function ($u) use ($normal) {
            $u->attachRole($normal);
        });
        factory(\App\Models\Notification::class, 100)->create();
    }
}
