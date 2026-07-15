<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bancos')) {
            return;
        }

        Schema::create('bancos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('banco_id');
            $table->string('banco_name', 500)->nullable();
            $table->string('banco_cod', 500)->nullable();
            $table->dateTime('banco_creator')->nullable();
            $table->integer('banco_area')->nullable();
            $table->enum('banco_status', array('Y', 'N', 'P'))->default('Y')->nullable();
            $table->string('banco_class', 500)->nullable();
            $table->integer('simulador')->nullable();
            $table->string('banco_curto', 500)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bancos');
    }
}
