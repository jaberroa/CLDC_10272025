#!/bin/bash

# Script para convertir migraciones de MySQL a PostgreSQL
# Este script actualiza automáticamente los tipos de datos problemáticos

echo "🔄 Convirtiendo migraciones de MySQL a PostgreSQL..."

# Directorio de migraciones
MIGRATIONS_DIR="database/migrations"

# Función para reemplazar tipos de datos
convert_migration_files() {
    echo "📝 Actualizando archivos de migración..."
    
    # Convertir json() a jsonb()
    find $MIGRATIONS_DIR -name "*.php" -exec sed -i 's/->json(/->jsonb(/g' {} \;
    
    # Convertir enum() a string() con check constraint
    find $MIGRATIONS_DIR -name "*.php" -exec sed -i 's/->enum(/->string(/g' {} \;
    
    # Convertir unsignedBigInteger() a bigInteger()
    find $MIGRATIONS_DIR -name "*.php" -exec sed -i 's/->unsignedBigInteger(/->bigInteger(/g' {} \;
    
    echo "✅ Archivos de migración actualizados"
}

# Función para crear constraints de validación para los enums convertidos
create_enum_constraints() {
    echo "🔒 Creando constraints de validación para enums..."
    
    # Crear archivo de constraints
    cat > database/migrations/2025_10_27_050001_create_postgresql_constraints.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Crear constraints de validación para los campos que eran enum
        $this->createEnumConstraints();
    }

    public function down(): void
    {
        $this->dropEnumConstraints();
    }

    private function createEnumConstraints(): void
    {
        $constraints = [
            // Comparticion documentos
            'comparticion_documentos' => [
                'tipo' => "CHECK (tipo IN ('interno', 'externo', 'publico'))"
            ],
            
            // Auditoria documentos
            'auditoria_documentos' => [
                'resultado' => "CHECK (resultado IN ('exito', 'error', 'bloqueado'))",
                'nivel' => "CHECK (nivel IN ('info', 'warning', 'critical'))"
            ],
            
            // Firmas electronicas
            'firmas_electronicas' => [
                'tipo' => "CHECK (tipo IN ('simple', 'secuencial', 'paralelo'))",
                'estado' => "CHECK (estado IN ('pendiente', 'en_proceso', 'completado', 'rechazado', 'cancelado'))"
            ],
            
            // Versiones documentos
            'versiones_documentos' => [
                'tipo_cambio' => "CHECK (tipo_cambio IN ('menor', 'mayor', 'critico'))"
            ],
            
            // Documentos gestion
            'documentos_gestion' => [
                'estado' => "CHECK (estado IN ('borrador', 'revision', 'aprobado', 'archivado', 'obsoleto'))"
            ],
            
            // Aprobaciones documentos
            'aprobaciones_documentos' => [
                'tipo' => "CHECK (tipo IN ('secuencial', 'paralelo', 'cualquiera'))"
            ],
            
            // Metadatos documentales
            'metadatos_documentales' => [
                'tipo' => "CHECK (tipo IN ('texto', 'numero', 'fecha', 'desplegable', 'checkbox', 'textarea', 'email', 'url', 'telefono'))"
            ],
            
            // Recordatorios documentos
            'recordatorios_documentos' => [
                'tipo' => "CHECK (tipo IN ('revision', 'vencimiento', 'renovacion', 'aprobacion', 'firma', 'personalizado'))",
                'frecuencia' => "CHECK (frecuencia IN ('una_vez', 'diaria', 'semanal', 'mensual', 'anual'))",
                'estado' => "CHECK (estado IN ('pendiente', 'enviado', 'completado', 'cancelado'))",
                'prioridad' => "CHECK (prioridad IN ('baja', 'normal', 'alta', 'urgente'))"
            ],
            
            // Cuotas membresia
            'cuotas_membresia' => [
                'tipo_cuota' => "CHECK (tipo_cuota IN ('mensual', 'trimestral', 'anual'))",
                'estado' => "CHECK (estado IN ('pendiente', 'pagada', 'vencida', 'cancelada'))"
            ],
            
            // Directivas
            'directivas' => [
                'tipo' => "CHECK (tipo IN ('directiva', 'comision', 'comite'))",
                'nivel' => "CHECK (nivel IN ('nacional', 'seccional', 'regional'))"
            ],
            
            // Cargos directiva
            'cargos_directiva' => [
                'nivel' => "CHECK (nivel IN ('nacional', 'seccional', 'regional'))"
            ],
            
            // Miembros
            'miembros' => [
                'estado' => "CHECK (estado IN ('activo', 'inactivo', 'suspendido'))"
            ],
            
            // Asambleas
            'asambleas' => [
                'tipo' => "CHECK (tipo IN ('ordinaria', 'extraordinaria', 'especial'))",
                'modalidad' => "CHECK (modalidad IN ('presencial', 'virtual', 'hibrida'))",
                'estado' => "CHECK (estado IN ('convocada', 'en_proceso', 'finalizada', 'cancelada'))"
            ],
            
            // Cronogramas directiva
            'cronogramas_directiva' => [
                'tipo' => "CHECK (tipo IN ('directiva', 'comision', 'especial'))",
                'estado' => "CHECK (estado IN ('programada', 'en_proceso', 'finalizada', 'cancelada'))"
            ],
            
            // Elecciones
            'elecciones' => [
                'estado' => "CHECK (estado IN ('activo', 'retirado', 'descalificado'))"
            ],
            
            // Capacitaciones
            'capacitaciones' => [
                'estado' => "CHECK (estado IN ('inscrito', 'completado', 'cancelado'))"
            ],
            
            // Comunicaciones
            'comunicaciones' => [
                'tipo' => "CHECK (tipo IN ('asamblea', 'comunicado', 'capacitacion', 'eleccion', 'transaccion'))",
                'estado' => "CHECK (estado IN ('borrador', 'publicada', 'archivada'))"
            ],
            
            // Transacciones financieras
            'transacciones_financieras' => [
                'tipo' => "CHECK (tipo IN ('ingreso', 'egreso'))",
                'estado' => "CHECK (estado IN ('pendiente', 'confirmada', 'cancelada'))"
            ],
            
            // Documentos
            'documentos' => [
                'tipo' => "CHECK (tipo IN ('acta', 'estatuto', 'reglamento', 'resolucion', 'otro'))",
                'estado' => "CHECK (estado IN ('borrador', 'aprobado', 'archivado'))"
            ]
        ];

        foreach ($constraints as $table => $tableConstraints) {
            if (Schema::hasTable($table)) {
                foreach ($tableConstraints as $column => $constraint) {
                    try {
                        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_{$column}_check {$constraint}");
                    } catch (Exception $e) {
                        // Constraint ya existe o error, continuar
                        echo "Constraint para {$table}.{$column} ya existe o error: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }

    private function dropEnumConstraints(): void
    {
        // Implementar drop de constraints si es necesario
    }
};
EOF
    
    echo "✅ Constraints de validación creados"
}

# Ejecutar conversiones
convert_migration_files
create_enum_constraints

echo "🎉 Conversión de MySQL a PostgreSQL completada!"
echo ""
echo "📋 Próximos pasos:"
echo "1. Actualiza tu archivo .env con la configuración de PostgreSQL"
echo "2. Ejecuta: docker-compose down -v (para limpiar volúmenes)"
echo "3. Ejecuta: docker-compose up -d --build"
echo "4. Ejecuta: docker-compose exec app php artisan migrate:fresh --seed"
echo ""
echo "🔧 Configuración .env recomendada:"
echo "DB_CONNECTION=pgsql"
echo "DB_HOST=db"
echo "DB_PORT=5432"
echo "DB_DATABASE=cldc_database"
echo "DB_USERNAME=cldc_user"
echo "DB_PASSWORD=cldc_password"
