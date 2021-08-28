<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotelImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motel_imgs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('motel_id',false,true);
            $table->foreign('motel_id')->references('id')->on('motels')->onDelete('cascade')->onUpdate('cascade');
            $table->string('place');
            $table->string('content');
            $table->bigInteger('img_type_id',false,true);
            $table->foreign('img_type_id')->references('id')->on('img_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('motel_imgs');
    }
}
