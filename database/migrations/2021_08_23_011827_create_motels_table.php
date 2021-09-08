<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id',false,true)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name') ;
            $table->string('address') ;
            $table->string('phone_number') ;
            $table->string('closed') ;
            $table->string('open') ;
            $table->string('parking') ;
            $table->integer('camera') ;
            $table->float('latitude')->default(0);
            $table->float('longitude')->default(0);
            $table->bigInteger('deposit') ;//dat coc
            $table->bigInteger('elec_cost') ;
            $table->bigInteger('water_cost') ;
            $table->bigInteger('people_cost') ;
            $table->string('content') ;
            $table->integer('auto_post');
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
        Schema::dropIfExists('motels');
    }
}
