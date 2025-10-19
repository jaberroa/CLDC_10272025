<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CldciDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear organización principal CLDCI
        $organizacionPrincipal = DB::table('organizaciones')->insertGetId([
            'id' => Str::uuid(),
            'nombre' => 'Círculo de Locutores Dominicanos Colegiados, Inc.',
            'codigo' => 'CLDCI-001',
            'tipo' => 'nacional',
            'pais' => 'República Dominicana',
            'provincia' => 'Distrito Nacional',
            'ciudad' => 'Santo Domingo',
            'direccion' => 'Ave. 27 de Febrero #1405, Plaza de la Cultura, Santo Domingo',
            'telefono' => '(809) 686-2583',
            'email' => 'info@cldci.org.do',
            'estado_adecuacion' => 'aprobada',
            'miembros_minimos' => 100,
            'fecha_fundacion' => '1990-03-15',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear seccionales provinciales (32 provincias de RD)
        $provincias = [
            'Azua', 'Baoruco', 'Barahona', 'Dajabón', 'Distrito Nacional',
            'Duarte', 'Elías Piña', 'El Seibo', 'Espaillat', 'Hato Mayor',
            'Hermanas Mirabal', 'Independencia', 'La Altagracia', 'La Romana',
            'La Vega', 'María Trinidad Sánchez', 'Monseñor Nouel', 'Monte Cristi',
            'Monte Plata', 'Pedernales', 'Peravia', 'Puerto Plata', 'Samaná',
            'Sánchez Ramírez', 'San Cristóbal', 'San José de Ocoa', 'San Juan',
            'San Pedro de Macorís', 'Santiago', 'Santiago Rodríguez', 'Santo Domingo',
            'Valverde', 'San José de las Matas'
        ];

        foreach ($provincias as $index => $provincia) {
            DB::table('organizaciones')->insert([
                'id' => Str::uuid(),
                'nombre' => 'CLDCI Seccional ' . $provincia,
                'codigo' => 'CLDCI-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'tipo' => 'seccional',
                'pais' => 'República Dominicana',
                'provincia' => $provincia,
                'estado_adecuacion' => 'pendiente',
                'miembros_minimos' => 15,
                'organizacion_padre_id' => $organizacionPrincipal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear seccionales internacionales (diáspora)
        $paisesDiaspora = [
            'Estados Unidos', 'España', 'Italia', 'Francia', 
            'Puerto Rico', 'Canadá', 'Venezuela', 'Colombia'
        ];

        foreach ($paisesDiaspora as $index => $pais) {
            DB::table('organizaciones')->insert([
                'id' => Str::uuid(),
                'nombre' => 'CLDCI Seccional Internacional ' . $pais,
                'codigo' => 'CLDCI-INT-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'tipo' => 'seccional_internacional',
                'pais' => $pais,
                'estado_adecuacion' => 'pendiente',
                'miembros_minimos' => 10,
                'organizacion_padre_id' => $organizacionPrincipal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear cargos base
        $cargos = [
            ['nombre' => 'Presidente', 'descripcion' => 'Máxima autoridad ejecutiva', 'nivel' => 'nacional', 'es_presidencia' => true, 'orden_prioridad' => 1],
            ['nombre' => 'Vicepresidente', 'descripcion' => 'Segunda autoridad ejecutiva', 'nivel' => 'nacional', 'es_presidencia' => false, 'orden_prioridad' => 2],
            ['nombre' => 'Secretario General', 'descripcion' => 'Responsable de documentación y actas', 'nivel' => 'nacional', 'es_presidencia' => false, 'orden_prioridad' => 3],
            ['nombre' => 'Tesorero', 'descripcion' => 'Responsable de finanzas', 'nivel' => 'nacional', 'es_presidencia' => false, 'orden_prioridad' => 4],
            ['nombre' => 'Director de Comunicación', 'descripcion' => 'Responsable de medios y relaciones públicas', 'nivel' => 'nacional', 'es_presidencia' => false, 'orden_prioridad' => 5],
            ['nombre' => 'Director de Formación', 'descripcion' => 'Responsable de capacitación profesional', 'nivel' => 'nacional', 'es_presidencia' => false, 'orden_prioridad' => 6],
            ['nombre' => 'Coordinador Seccional', 'descripcion' => 'Responsable de seccional provincial', 'nivel' => 'seccional', 'es_presidencia' => false, 'orden_prioridad' => 1],
            ['nombre' => 'Secretario Seccional', 'descripcion' => 'Secretario de seccional', 'nivel' => 'seccional', 'es_presidencia' => false, 'orden_prioridad' => 2],
            ['nombre' => 'Tesorero Seccional', 'descripcion' => 'Tesorero de seccional', 'nivel' => 'seccional', 'es_presidencia' => false, 'orden_prioridad' => 3],
        ];

        foreach ($cargos as $cargo) {
            DB::table('cargos')->insert([
                'id' => Str::uuid(),
                'nombre' => $cargo['nombre'],
                'descripcion' => $cargo['descripcion'],
                'nivel' => $cargo['nivel'],
                'es_presidencia' => $cargo['es_presidencia'],
                'orden_prioridad' => $cargo['orden_prioridad'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear órganos directivos
        $organos = [
            ['nombre' => 'Consejo Directivo Nacional', 'descripcion' => 'Máximo órgano de dirección', 'tipo' => 'direccion', 'nivel_jerarquico' => 1],
            ['nombre' => 'Presidencia', 'descripcion' => 'Ejecutivo principal', 'tipo' => 'direccion', 'nivel_jerarquico' => 2],
            ['nombre' => 'Dirección Ejecutiva', 'descripcion' => 'Órgano ejecutivo', 'tipo' => 'operativo', 'nivel_jerarquico' => 3],
            ['nombre' => 'Consejo Consultivo', 'descripcion' => 'Órgano consultivo', 'tipo' => 'consultivo', 'nivel_jerarquico' => 3],
            ['nombre' => 'Comisión Electoral', 'descripcion' => 'Órgano electoral', 'tipo' => 'especializado', 'nivel_jerarquico' => 3],
        ];

        foreach ($organos as $organo) {
            DB::table('organos_cldc')->insert([
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal,
                'nombre' => $organo['nombre'],
                'descripcion' => $organo['descripcion'],
                'tipo' => $organo['tipo'],
                'nivel_jerarquico' => $organo['nivel_jerarquico'],
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear miembros de demostración
        $miembrosDemo = [
            [
                'nombre_completo' => 'Dr. Juan Carlos Méndez Pérez',
                'email' => 'juan.mendez@cldci.org.do',
                'profesion' => 'Locutor Profesional Senior / Director de Programas',
                'numero_carnet' => 'CLDCI-2019-0001',
                'cedula' => '001-0123456-7',
                'telefono' => '(809) 555-1001',
                'fecha_ingreso' => now()->subYears(5)->format('Y-m-d'),
            ],
            [
                'nombre_completo' => 'Lcda. María Elena Rodríguez Santos',
                'email' => 'maria.rodriguez@cldci.org.do',
                'profesion' => 'Locutora Profesional / Productora Ejecutiva',
                'numero_carnet' => 'CLDCI-2021-0002',
                'cedula' => '001-0234567-8',
                'telefono' => '(809) 555-1002',
                'fecha_ingreso' => now()->subYears(3)->format('Y-m-d'),
            ],
            [
                'nombre_completo' => 'Lic. Roberto José García Jiménez',
                'email' => 'roberto.garcia@cldci.org.do',
                'profesion' => 'Director de Noticias / Analista Político',
                'numero_carnet' => 'CLDCI-2022-0003',
                'cedula' => '001-0345678-9',
                'telefono' => '(809) 555-1003',
                'fecha_ingreso' => now()->subYears(2)->format('Y-m-d'),
            ],
            [
                'nombre_completo' => 'Dra. Ana Patricia Jiménez López',
                'email' => 'ana.jimenez@cldci.org.do',
                'profesion' => 'Especialista en Comunicación / Consultora',
                'numero_carnet' => 'CLDCI-2023-0004',
                'cedula' => '001-0456789-0',
                'telefono' => '(809) 555-1004',
                'fecha_ingreso' => now()->subYear()->format('Y-m-d'),
            ],
            [
                'nombre_completo' => 'Lic. Carlos Alberto Santos Reyes',
                'email' => 'carlos.santos@cldci.org.do',
                'profesion' => 'Locutor Deportivo / Comentarista',
                'numero_carnet' => 'CLDCI-2024-0005',
                'cedula' => '001-0567890-1',
                'telefono' => '(809) 555-1005',
                'fecha_ingreso' => now()->subMonths(8)->format('Y-m-d'),
            ],
        ];

        foreach ($miembrosDemo as $miembro) {
            DB::table('miembros')->insert([
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal,
                'nombre_completo' => $miembro['nombre_completo'],
                'email' => $miembro['email'],
                'cedula' => $miembro['cedula'],
                'telefono' => $miembro['telefono'],
                'profesion' => $miembro['profesion'],
                'estado_membresia' => 'activa',
                'tipo_membresia' => 'activo',
                'fecha_ingreso' => $miembro['fecha_ingreso'],
                'numero_carnet' => $miembro['numero_carnet'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear cursos de demostración
        $cursosDemo = [
            [
                'titulo' => 'Locución Digital Profesional 2024',
                'descripcion' => 'Dominio de herramientas digitales modernas para la locución profesional',
                'modalidad' => 'presencial',
                'fecha_inicio' => now()->addDays(25)->format('Y-m-d'),
                'fecha_fin' => now()->addDays(27)->format('Y-m-d'),
                'capacidad_maxima' => 50,
                'lugar' => 'Sede Nacional CLDCI',
                'costo' => 3500.00,
            ],
            [
                'titulo' => 'Ética y Deontología del Comunicador',
                'descripcion' => 'Principios éticos fundamentales en el ejercicio de la comunicación',
                'modalidad' => 'virtual',
                'fecha_inicio' => now()->addDays(40)->format('Y-m-d'),
                'fecha_fin' => now()->addDays(42)->format('Y-m-d'),
                'capacidad_maxima' => 80,
                'lugar' => 'Plataforma Microsoft Teams',
                'costo' => 2000.00,
            ],
            [
                'titulo' => 'Gestión Integral de Medios Digitales',
                'descripcion' => 'Administración completa de contenido multimedia y redes sociales',
                'modalidad' => 'hibrida',
                'fecha_inicio' => now()->addDays(55)->format('Y-m-d'),
                'fecha_fin' => now()->addDays(57)->format('Y-m-d'),
                'capacidad_maxima' => 40,
                'lugar' => 'Modalidad Híbrida',
                'costo' => 4000.00,
            ],
        ];

        foreach ($cursosDemo as $curso) {
            DB::table('cursos')->insert([
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal,
                'titulo' => $curso['titulo'],
                'descripcion' => $curso['descripcion'],
                'tipo' => 'profesional',
                'modalidad' => $curso['modalidad'],
                'fecha_inicio' => $curso['fecha_inicio'],
                'fecha_fin' => $curso['fecha_fin'],
                'capacidad_maxima' => $curso['capacidad_maxima'],
                'lugar' => $curso['lugar'],
                'estado' => 'programada',
                'costo' => $curso['costo'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear asamblea de demostración
        DB::table('asambleas')->insert([
            'id' => Str::uuid(),
            'organizacion_id' => $organizacionPrincipal,
            'tipo' => 'ordinaria',
            'titulo' => 'Asamblea General Ordinaria CLDCI 2024',
            'descripcion' => 'Asamblea para presentación del informe anual, estados financieros, nuevos proyectos tecnológicos y elección de cargos vacantes de la Junta Directiva Nacional',
            'fecha_convocatoria' => now()->addDays(10)->format('Y-m-d'),
            'fecha_asamblea' => now()->addDays(60)->format('Y-m-d'),
            'quorum_minimo' => 75,
            'lugar' => 'Auditorio Nacional CLDCI, Ave. 27 de Febrero, Santo Domingo',
            'modalidad' => 'hibrida',
            'estado' => 'convocada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear presupuesto de demostración
        $presupuestoCategorias = [
            ['categoria' => 'Cuotas de Membresía Nacional', 'monto' => 1200000.00],
            ['categoria' => 'Eventos y Capacitaciones Profesionales', 'monto' => 350000.00],
            ['categoria' => 'Gastos Administrativos y Operativos', 'monto' => 280000.00],
            ['categoria' => 'Tecnología e Innovación Digital', 'monto' => 150000.00],
            ['categoria' => 'Comunicación y Relaciones Públicas', 'monto' => 120000.00],
            ['categoria' => 'Programas de Formación Continua', 'monto' => 200000.00],
        ];

        foreach ($presupuestoCategorias as $categoria) {
            DB::table('presupuestos')->insert([
                'id' => Str::uuid(),
                'organizacion_id' => $organizacionPrincipal,
                'categoria' => $categoria['categoria'],
                'periodo' => '2024',
                'monto_presupuestado' => $categoria['monto'],
                'monto_ejecutado' => $categoria['monto'] * 0.3, // 30% ejecutado
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear padrón electoral
        DB::table('padrones_electorales')->insert([
            'id' => Str::uuid(),
            'organizacion_id' => $organizacionPrincipal,
            'periodo' => '2024-2026',
            'descripcion' => 'Padrón Electoral Nacional CLDCI 2024-2026 - Registro oficial de miembros con derecho al voto',
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2026-12-31',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Datos CLDCI creados exitosamente:');
        $this->command->info('   - Organización principal: CLDCI');
        $this->command->info('   - Seccionales provinciales: ' . count($provincias));
        $this->command->info('   - Seccionales internacionales: ' . count($paisesDiaspora));
        $this->command->info('   - Miembros de demostración: ' . count($miembrosDemo));
        $this->command->info('   - Cursos programados: ' . count($cursosDemo));
        $this->command->info('   - Asamblea convocada: 1');
        $this->command->info('   - Presupuesto 2024: 6 categorías');
        $this->command->info('   - Padrón electoral: Activo 2024-2026');
    }
}
