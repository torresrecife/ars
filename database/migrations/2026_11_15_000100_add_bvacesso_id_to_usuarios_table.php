<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Liga o usuário à conta do login único (BVAcesso).
        if (! Schema::hasColumn('usuarios', 'bvacesso_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('bvacesso_id', 40)->nullable()->unique()->after('id_usu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'bvacesso_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropUnique(['bvacesso_id']);
                $table->dropColumn('bvacesso_id');
            });
        }
    }
};
