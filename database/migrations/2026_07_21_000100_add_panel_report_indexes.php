<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPanelReportIndexes extends Migration
{
    public function up()
    {
        Schema::table('metas_andamentos', function (Blueprint $table) {
            $table->index(
                array('banco_id', 'meta_mes', 'meta_ano', 'regiao_id', 'anda_id'),
                'idx_metas_bank_month_year_region_anda'
            );
            $table->index(
                array('banco_id', 'meta_mes', 'meta_ano', 'anda_id'),
                'idx_metas_bank_month_year_anda'
            );
        });

        Schema::table('semanas', function (Blueprint $table) {
            $table->index(array('mes', 'ano'), 'idx_semanas_mes_ano');
        });

        Schema::table('carteira', function (Blueprint $table) {
            $table->index(array('banco_id', 'carteira_condicao'), 'idx_carteira_banco_condicao');
        });

        Schema::table('dados', function (Blueprint $table) {
            $table->index(array('banco_id'), 'idx_dados_banco_id');
            $table->index(array('carteira_id'), 'idx_dados_carteira_id');
        });

        Schema::table('bancos', function (Blueprint $table) {
            $table->index(array('banco_area', 'banco_status', 'banco_id'), 'idx_bancos_area_status_id');
        });

        Schema::table('andamentos', function (Blueprint $table) {
            $table->index(array('especie', 'ordem', 'nome'), 'idx_andamentos_especie_ordem_nome');
        });
    }

    public function down()
    {
        Schema::table('andamentos', function (Blueprint $table) {
            $table->dropIndex('idx_andamentos_especie_ordem_nome');
        });

        Schema::table('bancos', function (Blueprint $table) {
            $table->dropIndex('idx_bancos_area_status_id');
        });

        Schema::table('dados', function (Blueprint $table) {
            $table->dropIndex('idx_dados_banco_id');
            $table->dropIndex('idx_dados_carteira_id');
        });

        Schema::table('carteira', function (Blueprint $table) {
            $table->dropIndex('idx_carteira_banco_condicao');
        });

        Schema::table('semanas', function (Blueprint $table) {
            $table->dropIndex('idx_semanas_mes_ano');
        });

        Schema::table('metas_andamentos', function (Blueprint $table) {
            $table->dropIndex('idx_metas_bank_month_year_region_anda');
            $table->dropIndex('idx_metas_bank_month_year_anda');
        });
    }
}
