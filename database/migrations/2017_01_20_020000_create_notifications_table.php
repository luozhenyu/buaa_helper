<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 160);
            $table->integer('user_id')->unsigned();
            $table->integer('department_id')->unsigned();
            $table->timestamp('start_time')->default('2017-01-01 00:00:00');
            $table->timestamp('end_time')->default('2017-01-02 00:00:00');
            $table->boolean('important');
            $table->mediumText('content');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE `notifications` ADD FULLTEXT INDEX `ft_index`(`title`,`content`) WITH PARSER ngram;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
