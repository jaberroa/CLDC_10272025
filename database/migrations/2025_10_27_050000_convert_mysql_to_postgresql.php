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
        // Convertir tipos de datos de MySQL a PostgreSQL
        $this->convertJsonToJsonb();
        $this->convertEnumToString();
        $this->convertUnsignedBigIntegerToBigInteger();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios si es necesario
        $this->revertJsonbToJson();
        $this->revertStringToEnum();
        $this->revertBigIntegerToUnsignedBigInteger();
    }

    private function convertJsonToJsonb(): void
    {
        // Lista de tablas y columnas que usan json
        $jsonColumns = [
            'app_roles' => ['permisos'],
            'carpetas_documentales' => ['permisos_personalizados'],
            'versiones_documentos' => ['cambios'],
            'auditoria_documentos' => ['datos_anteriores', 'datos_nuevos', 'metadatos'],
            'aprobaciones_documentos' => ['escalacion_usuarios'],
            'recordatorios_documentos' => ['usuarios_ids', 'emails_externos', 'usuarios_escalacion'],
            'permisos_documentales' => ['permisos', 'permisos_personalizados'],
            'metadatos_documentales' => ['opciones'],
            'comentarios_documentos' => ['menciones', 'archivos_adjuntos', 'coordenadas'],
            'secciones_documentales' => ['permisos_defecto', 'formatos_permitidos'],
            'cronogramas_directiva' => ['participantes', 'agenda'],
            'miembros' => ['personalizacion_json'],
        ];

        foreach ($jsonColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($table->getTable(), $column)) {
                            $table->jsonb($column)->nullable()->change();
                        }
                    }
                });
            }
        }
    }

    private function convertEnumToString(): void
    {
        // Lista de tablas y columnas que usan enum
        $enumColumns = [
            'comparticion_documentos' => ['tipo'],
            'auditoria_documentos' => ['resultado', 'nivel'],
            'firmas_electronicas' => ['tipo', 'estado'],
            'versiones_documentos' => ['tipo_cambio'],
            'documentos_gestion' => ['estado'],
            'aprobaciones_documentos' => ['tipo'],
            'metadatos_documentales' => ['tipo'],
            'recordatorios_documentos' => ['tipo', 'frecuencia', 'estado', 'prioridad'],
            'cuotas_membresia' => ['tipo_cuota', 'estado'],
            'directivas' => ['tipo', 'nivel'],
            'cargos_directiva' => ['nivel'],
            // 'miembros' => ['estado'], // Removido: la columna estado se agregará en otra migración
            'asambleas' => ['tipo', 'modalidad', 'estado'],
            'cronogramas_directiva' => ['tipo', 'estado'],
            'elecciones' => ['estado'],
            'capacitaciones' => ['estado'],
            'comunicaciones' => ['tipo', 'estado'],
            'transacciones_financieras' => ['tipo', 'estado'],
            'documentos' => ['tipo', 'estado'],
        ];

        foreach ($enumColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($table->getTable(), $column)) {
                            $table->string($column)->change();
                        }
                    }
                });
            }
        }
    }

    private function convertUnsignedBigIntegerToBigInteger(): void
    {
        // Lista de tablas y columnas que usan unsignedBigInteger
        $unsignedColumns = [
            'voting_tokens' => ['eleccion_id'],
            'carpetas_documentales' => ['entidad_id'],
            'auditoria_documentos' => ['entidad_id'],
            'documentos_gestion' => ['entidad_id'],
        ];

        foreach ($unsignedColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($table->getTable(), $column)) {
                            $table->bigInteger($column)->change();
                        }
                    }
                });
            }
        }
    }

    private function revertJsonbToJson(): void
    {
        // Revertir jsonb a json si es necesario
        // Implementar según necesidades específicas
    }

    private function revertStringToEnum(): void
    {
        // Revertir string a enum si es necesario
        // Implementar según necesidades específicas
    }

    private function revertBigIntegerToUnsignedBigInteger(): void
    {
        // Revertir bigInteger a unsignedBigInteger si es necesario
        // Implementar según necesidades específicas
    }
};
