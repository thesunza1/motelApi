<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id',false,true);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('status')->default(0);
            $table->timestamp('date_begin');
            $table->timestamp('date_end');
            $table->integer('elec_begin');
            $table->integer('elec_end')->nullable();
            $table->integer('water_begin');
            $table->integer('water_end')->nullable();
            $table->integer('cost');
            $table->integer('water_cost');
            $table->integer('elec_cost');
            $table->integer('people_cost');
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
        Schema::dropIfExists('bills');
    }
}
