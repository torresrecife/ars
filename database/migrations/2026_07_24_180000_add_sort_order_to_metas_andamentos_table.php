<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortOrderToMetasAndamentosTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('metas_andamentos')) {
            return;
        }

        Schema::table('metas_andamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('metas_andamentos', 'sort_order')) {
                $table->unsignedInteger('sort_order')->nullable()->after('regiao_id');
                $table->index('sort_order', 'idx_metas_andamentos_sort_order');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('metas_andamentos')) {
            return;
        }

        Schema::table('metas_andamentos', function (Blueprint $table) {
            if (Schema::hasColumn('metas_andamentos', 'sort_order')) {
                $table->dropIndex('idx_metas_andamentos_sort_order');
                $table->dropColumn('sort_order');
            }
        });
    }
}
