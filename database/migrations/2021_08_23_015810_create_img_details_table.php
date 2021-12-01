<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImgDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('img_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('motel_img_id',false,true)->nullable();
            $table->bigInteger('room_type_id',false,true)->nullable();
            $table->bigInteger('tenant_room_equip_id',false,true)->nullable();
            $table->foreign('motel_img_id')->references('id')->on('motel_imgs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tenant_room_equip_id')->references('id')->on('tenant_room_equips')->onDelete('cascade')->onUpdate('cascade');
            $table->string('img');
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
        Schema::dropIfExists('img_details');
    }
}
