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
        Schema::table('tipos_membresia', function (Blueprint $table) {
            if (!Schema::hasColumn('tipos_membresia', 'descripcion')) {
                $table->string('descripcion')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipos_membresia', function (Blueprint $table) {
            if (Schema::hasColumn('tipos_membresia', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
    }
};
