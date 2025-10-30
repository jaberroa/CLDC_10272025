<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transacciones_financieras')) {
            Schema::table('transacciones_financieras', function (Blueprint $table) {
                $table->unsignedBigInteger('cuota_id')->nullable()->after('id');
                $table->foreign('cuota_id')->references('id')->on('cuotas_membresia')->onDelete('set null');
                $table->unique(['cuota_id'], 'unique_transaccion_por_cuota');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transacciones_financieras')) {
            Schema::table('transacciones_financieras', function (Blueprint $table) {
                $table->dropUnique('unique_transaccion_por_cuota');
                $table->dropForeign(['cuota_id']);
                $table->dropColumn('cuota_id');
            });
        }
    }
};


