<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRegioesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('regioes')) {
            return;
        }

        Schema::create('regioes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->increments('regiao_id');
            $table->string('regiao_nome', 100)->unique('uk_regioes_nome');
            $table->string('regiao_slug', 100)->unique('uk_regioes_slug');
            $table->char('regiao_status', 1)->default('Y');
            $table->dateTime('data_cad')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('data_alt')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('regioes');
    }
}
