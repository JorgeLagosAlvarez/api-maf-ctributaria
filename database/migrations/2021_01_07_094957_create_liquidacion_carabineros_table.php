<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiquidacionCarabinerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liquidacion_carabineros', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->string('document_type');
            $table->string('file_name');
            $table->string('ext');
            $table->string('nro_liquidacion');
            $table->string('nombre_cliente');
            $table->string('rut_cliente');
            $table->integer('carga_familiar');
            $table->integer('total_haber');
            $table->integer('descuentos_legales');
            $table->integer('monto_liquido');
            $table->string('periodo');
            $table->string('id_solicitud');
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
        Schema::dropIfExists('liquidacion_carabineros');
    }
}
