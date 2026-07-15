<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosRegioesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('usuarios_regioes')) {
            return;
        }

        Schema::create('usuarios_regioes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->increments('usuario_regiao_id');
            $table->integer('usuario_id');
            $table->unsignedInteger('regiao_id');

            $table->unique(array('usuario_id', 'regiao_id'), 'uk_usuario_regiao');
            $table->index('usuario_id', 'idx_usuario_id');
            $table->index('regiao_id', 'idx_regiao_id');
            $table->foreign('regiao_id', 'fk_usuarios_regioes_regiao')
                ->references('regiao_id')
                ->on('regioes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('usuario_id', 'fk_usuarios_regioes_usuario')
                ->references('id_usu')
                ->on('usuarios')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios_regioes');
    }
}
