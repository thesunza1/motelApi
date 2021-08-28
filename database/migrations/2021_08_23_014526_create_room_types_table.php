<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name') ;
            $table->bigInteger('motel_id',false, true);
            $table->foreign('motel_id')->references('id')->on('motels')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('area');
            $table->integer('cost');
            $table->integer('male');
            $table->integer('female');
            $table->integer('everyone');
            $table->integer('content');
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
        Schema::dropIfExists('room_types');
    }
}
