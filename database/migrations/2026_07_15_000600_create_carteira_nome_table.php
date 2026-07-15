<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteiraNomeTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('carteira_nome')) {
            return;
        }

        Schema::create('carteira_nome', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('carteira_cod');
            $table->string('carteira_condicao', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carteira_nome');
    }
}
