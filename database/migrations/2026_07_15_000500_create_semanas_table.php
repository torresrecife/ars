<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemanasTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('semanas')) {
            return;
        }

        Schema::create('semanas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('semanas_id');
            $table->integer('mes')->nullable();
            $table->integer('ano')->nullable();
            $table->integer('ini_1')->nullable();
            $table->integer('fim_1')->nullable();
            $table->integer('ini_2')->nullable();
            $table->integer('fim_2')->nullable();
            $table->integer('ini_3')->nullable();
            $table->integer('fim_3')->nullable();
            $table->integer('ini_4')->nullable();
            $table->integer('fim_4')->nullable();
            $table->integer('ini_5')->nullable();
            $table->integer('fim_5')->nullable();
            $table->timestamp('data_cad')->nullable();
            $table->timestamp('data_arlt')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('semanas');
    }
}
