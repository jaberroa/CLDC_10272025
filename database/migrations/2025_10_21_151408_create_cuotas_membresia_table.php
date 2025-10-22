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
        Schema::create('cuotas_membresia', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('miembro_id');
            $table->string('tipo_cuota'); // 'mensual', 'trimestral', 'anual'
            $table->decimal('monto', 12, 2);
            $table->date('fecha_vencimiento');
            $table->string('estado')->default('pendiente'); // 'pendiente', 'pagada', 'vencida'
            $table->date('fecha_pago')->nullable();
            $table->string('metodo_pago')->nullable(); // 'efectivo', 'transferencia', 'tarjeta'
            $table->string('comprobante_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
            
            $table->index(['miembro_id', 'estado']);
            $table->index(['fecha_vencimiento', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_membresia');
    }
};