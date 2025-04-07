<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar user_id a la tabla ingresos solo si no existe
        Schema::table('ingresos', function (Blueprint $table) {
            if (!Schema::hasColumn('ingresos', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Agregar user_id a la tabla gastos solo si no existe
        Schema::table('gastos', function (Blueprint $table) {
            if (!Schema::hasColumn('gastos', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ingresos', function (Blueprint $table) {
            if (Schema::hasColumn('ingresos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('gastos', function (Blueprint $table) {
            if (Schema::hasColumn('gastos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
