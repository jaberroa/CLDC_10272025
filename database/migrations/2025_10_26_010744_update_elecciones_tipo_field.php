<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar el tipo de ENUM a VARCHAR para más flexibilidad
        DB::statement("ALTER TABLE elecciones MODIFY COLUMN tipo VARCHAR(50) NOT NULL DEFAULT 'junta_directiva'");
    }

    public function down(): void
    {
        // Revertir a los valores originales del ENUM
        DB::statement("ALTER TABLE elecciones MODIFY COLUMN tipo ENUM('directiva', 'comision', 'especial') NOT NULL DEFAULT 'directiva'");
    }
};
