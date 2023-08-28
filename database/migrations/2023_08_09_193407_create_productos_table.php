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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->integer('estatus')->nullable();
            $table->decimal('precio1', 10, 0)->nullable();
            $table->decimal('precio2', 10, 0)->nullable();
            $table->decimal('precio3', 10, 0)->nullable();
            $table->integer('id_categoria')->nullable();
            $table->integer('imagen')->nullable();
            $table->string('presentacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
