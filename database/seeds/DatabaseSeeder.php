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
        $this->call(CityTableSeeder::class);
        $this->call(DepartmentTableSeeder::class);
        $this->call(PropertyTableSeeder::class);
        $this->call(UserTableSeeder::class);

    }
}
