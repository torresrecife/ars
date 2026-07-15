<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteiraTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('carteira')) {
            return;
        }

        Schema::create('carteira', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('carteira_id');
            $table->integer('banco_id')->nullable();
            $table->string('carteira_condicao', 500)->nullable();
            $table->integer('carteira_cod')->nullable();
            $table->enum('carteira_vinc', array('LIKE', '=', 'IN', 'NOT IN'))->nullable();
            $table->dateTime('carteira_date')->nullable();
            $table->enum('carteira_status', array('Y', 'N'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carteira');
    }
}
