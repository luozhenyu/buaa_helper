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
            $table->integer('number')->unsigned();
            $table->integer('department_id')->unsigned();
            $table->string('name', 80);
            $table->string('email', 80)->nullable();
            $table->boolean('email_verified')->default(false);
            $table->bigInteger('phone')->unsigned()->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->char('password', 60)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->inherits('users');
            $table->integer('avatar')->unsigned()->nullable();

            $table->primary('id');
        });

        Schema::create('super_admins', function (Blueprint $table) {
            $table->inherits('admins');

            $table->primary('id');
        });

        Schema::create('department_admins', function (Blueprint $table) {
            $table->inherits('admins');

            $table->primary('id');
        });

        Schema::create('counsellors', function (Blueprint $table) {
            $table->inherits('admins');

            $table->primary('id');
        });

        Schema::create('students', function (Blueprint $table) {
            $table->inherits('users');

            $table->integer('avatar')->unsigned()->nullable();

            $table->integer('grade')->unsigned()->nullable();
            $table->integer('class')->unsigned()->nullable();
            $table->integer('political_status')->unsigned()->nullable();
            $table->integer('native_place')->unsigned()->nullable();
            $table->integer('financial_difficulty')->unsigned()->nullable();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('super_admins');
        Schema::dropIfExists('department_admins');
        Schema::dropIfExists('counsellors');
        Schema::dropIfExists('admins');

        Schema::dropIfExists('students');

        Schema::dropIfExists('users');
    }
}
