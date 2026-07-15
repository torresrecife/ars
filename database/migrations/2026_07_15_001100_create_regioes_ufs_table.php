<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegioesUfsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('regioes_ufs')) {
            return;
        }

        Schema::create('regioes_ufs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->increments('regiao_uf_id');
            $table->unsignedInteger('regiao_id');
            $table->char('uf', 2);

            $table->unique(array('regiao_id', 'uf'), 'uk_regiao_uf');
            $table->index('uf', 'idx_uf');
            $table->foreign('regiao_id', 'fk_regioes_ufs_regiao')
                ->references('regiao_id')
                ->on('regioes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('regioes_ufs');
    }
}
