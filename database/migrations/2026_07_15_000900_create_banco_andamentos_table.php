<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancoAndamentosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('banco_andamentos')) {
            return;
        }

        Schema::create('banco_andamentos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('ba_id');
            $table->integer('banco_id')->default(0);
            $table->integer('anda_id')->default(0);
            $table->enum('stt', array('Y', 'N'))->default('Y');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banco_andamentos');
    }
}
