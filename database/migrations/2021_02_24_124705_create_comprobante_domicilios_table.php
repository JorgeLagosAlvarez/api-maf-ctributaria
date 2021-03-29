<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprobanteDomiciliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobante_domicilios', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->bigInteger('proveedor_servicio_id')->unsigned();
            $table->foreign('proveedor_servicio_id')->references('id')->on('proveedor_servicios');

            $table->string('document_type');
            $table->string('id_solicitud');
            $table->string('nro_cliente');
            $table->string('nro_boleta')->nullable();
            $table->string('nombre_cliente')->nullable();
            $table->string('direccion_cliente');
            $table->string('comuna_cliente');
            $table->date('fecha_emision');
            $table->integer('monto')->default(0);
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
        Schema::dropIfExists('comprobante_domicilios');
    }
}
