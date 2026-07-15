<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetasAndamentosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('metas_andamentos')) {
            return;
        }

        Schema::create('metas_andamentos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('meta_id');
            $table->integer('banco_id')->nullable();
            $table->integer('meta_mes')->nullable();
            $table->integer('meta_ano')->nullable();
            $table->integer('anda_id')->nullable();
            $table->decimal('meta_valor', 10, 2)->nullable();
            $table->enum('def_sem', array('Y', 'N'))->default('N')->nullable();
            $table->decimal('sem_1', 10, 2)->nullable();
            $table->decimal('sem_2', 10, 2)->nullable();
            $table->decimal('sem_3', 10, 2)->nullable();
            $table->decimal('sem_4', 10, 2)->nullable();
            $table->decimal('sem_5', 10, 2)->nullable();
            $table->unsignedInteger('regiao_id')->nullable();

            $table->index('regiao_id', 'idx_metas_andamentos_regiao_id');
            $table->foreign('regiao_id', 'fk_metas_andamentos_regiao')
                ->references('regiao_id')
                ->on('regioes')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('metas_andamentos');
    }
}
