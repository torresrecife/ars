<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios')) {
            return;
        }

        Schema::create('usuarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('id_usu');
            $table->integer('id_neo')->default(0);
            $table->string('nome_usu', 50)->nullable();
            $table->string('login_usu', 50)->nullable();
            $table->string('senha_usu', 100)->nullable();
            $table->string('email_usu', 50)->nullable();
            $table->enum('nivel_usu', array('ADM', 'GER', 'USU'))->default('USU')->nullable();
            $table->dateTime('acesso_usu')->nullable();
            $table->dateTime('data_cad')->nullable();
            $table->integer('id_setor')->nullable();
            $table->string('id_cliente', 50)->nullable();
            $table->char('regiao_modo', 1)->default('N');
            $table->enum('status_usu', array('ATI', 'INA'))->default('ATI')->nullable();
            $table->string('estados_usu', 50)->default('');
            $table->string('comarca_usu', 50)->default('');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
