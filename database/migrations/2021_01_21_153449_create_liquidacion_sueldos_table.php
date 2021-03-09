<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiquidacionSueldosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liquidacion_sueldos', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->string('document_type');
            $table->string('id_solicitud');
            $table->string('afp');
            $table->string('mes');
            $table->string('anio');
            $table->string('rut')->nullable();
            $table->integer('impuesto');
            $table->integer('monto_bruto')->default(0);
            $table->integer('total_imponible')->default(0);
            $table->integer('total_haberes')->default(0);
            $table->integer('total_tributable')->default(0);
            $table->integer('apv');
            $table->integer('ajustes');
            $table->string('prevision');
            $table->integer('monto_salud_1');
            $table->integer('monto_salud_2')->default(0);
            $table->string('exento_seguro_cesantia');
            $table->integer('seguro_cesantia')->default(0);
            $table->string('workitemid')->unique();
            $table->string('tipo_contrato')->default("0");
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
        Schema::dropIfExists('liquidacion_sueldos');
    }
}
