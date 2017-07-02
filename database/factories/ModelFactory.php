<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'number' => $faker->unique()->randomNumber(8),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->unique()->randomNumber(8),
        'remember_token' => str_random(10),
        'department_id' => ($num = $faker->numberBetween(1, 20)) > 10 ? $num + 90 : $num,
    ];
});

$factory->define(App\Models\Notification::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'user_id' => 1,
        'department_id' => 21,
        'start_date' => '2017-07-01 00:00:00',
        'finish_date' => '2017-09-01 00:00:00',
        'important' => false,
        'excerpt' => $faker->word,
        'content' => $faker->paragraph,
    ];
});
