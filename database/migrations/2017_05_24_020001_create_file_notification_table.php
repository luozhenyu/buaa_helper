<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_notification', function (Blueprint $table) {
            $table->integer('notification_id')->unsigned();
            $table->integer('file_id')->unsigned();
            $table->timestamps();

            $table->foreign('notification_id')->references('id')->on('notifications')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('files')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['notification_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_notification');
    }
}
