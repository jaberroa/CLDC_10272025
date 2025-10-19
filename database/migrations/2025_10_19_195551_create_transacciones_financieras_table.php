<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizacion_id');
            $table->string('tipo'); // 'ingreso', 'gasto'
            $table->string('categoria'); // 'cuotas', 'eventos', 'patrocinios', 'operativo', etc
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('comprobante_url')->nullable();
            $table->string('metodo_pago')->nullable(); // 'efectivo', 'transferencia', 'tarjeta', etc
            $table->string('referencia')->nullable();
            $table->uuid('aprobado_por')->nullable();
            $table->text('observaciones')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('organizacion_id')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['organizacion_id', 'fecha', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_financieras');
    }
};