<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use LuoZhenyu\PostgresFullText\Blueprint;

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
            $table->json('target')->nullable();

            $table->mediumText('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->fulltext(['title', 'content']);
        });
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
