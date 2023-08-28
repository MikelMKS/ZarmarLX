<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre')->nullable();
            $table->integer('estatus')->nullable();
            $table->string('presentacion_1')->nullable();
            $table->string('presentacion_1_descr')->nullable();
            $table->string('presentacion_2')->nullable();
            $table->string('presentacion_2_descr')->nullable();
            $table->string('presentacion_3')->nullable();
            $table->string('presentacion_3_descr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
};
