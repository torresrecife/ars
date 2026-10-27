<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemporaryPasswordFieldsToUsuariosTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('usuarios', 'must_change_password')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->boolean('must_change_password')->default(false)->after('acesso_usu');
            });
        }

        if (!Schema::hasColumn('usuarios', 'temporary_password_expires_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dateTime('temporary_password_expires_at')->nullable()->after('must_change_password');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('usuarios', 'temporary_password_expires_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('temporary_password_expires_at');
            });
        }

        if (Schema::hasColumn('usuarios', 'must_change_password')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('must_change_password');
            });
        }
    }
}
