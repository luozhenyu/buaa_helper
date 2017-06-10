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
            $table->timestamp('start_date')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->boolean('important');
            $table->string('excerpt', 280);
            $table->mediumText('content');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE `notifications` ADD FULLTEXT INDEX `ft_index`(`title`,`excerpt`,`content`) WITH PARSER ngram;');
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
