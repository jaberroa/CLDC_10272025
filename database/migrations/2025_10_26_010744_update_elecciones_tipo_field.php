<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar el tipo de ENUM a VARCHAR para más flexibilidad (PostgreSQL)
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo SET DEFAULT 'junta_directiva'");
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo SET NOT NULL");
    }

    public function down(): void
    {
        // Revertir a los valores originales del ENUM (PostgreSQL)
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo SET DEFAULT 'directiva'");
        DB::statement("ALTER TABLE elecciones ALTER COLUMN tipo SET NOT NULL");
    }
};
