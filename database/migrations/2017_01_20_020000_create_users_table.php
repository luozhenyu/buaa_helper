<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number')->unsigned()->unique();
            $table->integer('department_id')->unsigned();
            $table->string('name', 80);
            $table->string('email', 80)->nullable()->unique();
            $table->bigInteger('phone')->unsigned()->nullable()->unique();
            $table->char('password', 60)->nullable();
            $table->integer('avatar')->unsigned()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
