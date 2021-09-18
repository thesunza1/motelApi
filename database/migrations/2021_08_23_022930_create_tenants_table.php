<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->integer('eq_status')->default(0);
            $table->bigInteger('room_id',false,true);
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('water_num')->nullable();
            $table->integer('elec_num')->nullable();
            $table->integer('num_status')->default(0);
            $table->integer('status')->default(0); // 0 con hoat dong . 1 het hoat dong.
            $table->timestamp('in_date')->useCurrent();
            $table->timestamp('out_date')->nullable();
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
        Schema::dropIfExists('tenants');
    }
}
