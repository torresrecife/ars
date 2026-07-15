<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('area')) {
            return;
        }

        Schema::create('area', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('area_id');
            $table->string('area_nome', 500)->nullable();
            $table->dateTime('area_date')->nullable();
            $table->enum('area_status', array('Y', 'N'))->default('Y')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('area');
    }
}
