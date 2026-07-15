<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('dados')) {
            return;
        }

        Schema::create('dados', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('dados_id');
            $table->integer('carteira_id')->nullable();
            $table->integer('banco_id')->nullable();
            $table->string('dados_cod', 1000)->nullable();
            $table->dateTime('dados_date')->nullable();
            $table->enum('dados_status', array('Y', 'N'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dados');
    }
}
