<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSituacionTributariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('situacion_tributarias', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->string('document_type');
            $table->string('id_solicitud');
            $table->string('rut_contribuyente');
            $table->string('workitemid')->unique();
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
        Schema::dropIfExists('situacion_tributarias');
    }
}
