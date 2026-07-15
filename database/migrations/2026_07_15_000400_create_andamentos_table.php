<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAndamentosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('andamentos')) {
            return;
        }

        Schema::create('andamentos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('anda_id');
            $table->string('nome', 50)->nullable();
            $table->string('chave', 50)->nullable();
            $table->string('anda_neo', 1000)->nullable();
            $table->integer('especie')->nullable();
            $table->enum('painel', array('Y', 'N'))->default('N')->nullable();
            $table->string('titulo', 50)->nullable();
            $table->integer('ordem')->nullable();
            $table->enum('repete', array('Y', 'N'))->default('Y')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('andamentos');
    }
}
