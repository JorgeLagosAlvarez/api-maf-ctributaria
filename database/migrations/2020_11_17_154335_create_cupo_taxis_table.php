<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupoTaxisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupo_taxis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->string('document_type');
            $table->string('id_solicitud');
            $table->string('patente');
            $table->string('workitemid')->nullable();
            $table->boolean('validation')->default(false);
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
        Schema::dropIfExists('cupo_taxis');
    }
}
