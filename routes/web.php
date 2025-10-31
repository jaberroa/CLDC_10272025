<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MiembrosController;
use App\Http\Controllers\OrganizacionesController;
use App\Http\Controllers\DirectivaController;
use App\Http\Controllers\CarnetController;
use App\Http\Controllers\CronogramaDirectivaController;
use App\Http\Controllers\AsambleaController;
use App\Http\Controllers\CapacitacionController;

// Cargar rutas de autenticación
require_once __DIR__ . '/auth.php';


// Cargar rutas de gestión documental
require_once __DIR__ . '/gestion-documental.php';

// API para búsqueda de usuarios (para compartir documentos)
Route::get('/api/usuarios/buscar', function(\Illuminate\Http\Request $request) {
    $query = $request->input('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $usuarios = \App\Models\User::where('name', 'LIKE', "%{$query}%")
        ->orWhere('email', 'LIKE', "%{$query}%")
        ->select('id', 'name', 'email')
        ->limit(10)
        ->get();
    
    return response()->json($usuarios);
})->middleware('auth');

// API para obtener candidatos de una elección
Route::get('/api/elecciones/{eleccion}/candidatos', function($eleccionId) {
    try {
        $eleccion = \App\Models\Eleccion::with(['candidatos.miembro', 'candidatos.cargo'])->findOrFail($eleccionId);
        
        $candidatos = $eleccion->candidatos->map(function($candidato) {
            return [
                'id' => $candidato->id,
                'nombre' => $candidato->nombre ?? ($candidato->miembro->nombre_completo ?? 'N/A'),
                'cargo' => $candidato->cargo ?? ($candidato->cargo ? $candidato->cargo->nombre : 'N/A'),
                'propuestas' => $candidato->propuestas,
                'orden' => $candidato->orden ?? 0,
                'activo' => $candidato->activo ?? true,
            ];
        })->sortBy('orden')->values();
        
        return response()->json([
            'success' => true,
            'candidatos' => $candidatos,
            'total' => $candidatos->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener candidatos: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});



// Votación pública con token (SIN AUTH - acceso público)
Route::get('/vote', [App\Http\Controllers\VotingLinkController::class, 'mostrarVotacion'])->name('voting.mostrar');
Route::post('/vote/submit', [App\Http\Controllers\VotingLinkController::class, 'registrarVoto'])->name('voting.submit');

// Redirección de /orgs a /organizaciones (mantener compatibilidad)
Route::get('/orgs', function() {
    return redirect()->route('organizaciones.index');
});

// Rutas de organizaciones
Route::get('/organizaciones', [OrganizacionesController::class, 'index'])->name('organizaciones.index');
Route::get('/organizaciones/create', [OrganizacionesController::class, 'create'])->name('organizaciones.create');
Route::post('/organizaciones', [OrganizacionesController::class, 'store'])->name('organizaciones.store');
Route::get('/organizaciones/{id}/profile', [OrganizacionesController::class, 'profile'])->name('organizaciones.profile');
Route::get('/organizaciones/{id}/edit', [OrganizacionesController::class, 'edit'])->name('organizaciones.edit');
Route::put('/organizaciones/{id}', [OrganizacionesController::class, 'update'])->name('organizaciones.update');
Route::delete('/organizaciones/{id}', [OrganizacionesController::class, 'destroy'])->name('organizaciones.destroy');
Route::post('/organizaciones/{id}/activate', [OrganizacionesController::class, 'activate'])->name('organizaciones.activate');
Route::post('/organizaciones/{id}/deactivate', [OrganizacionesController::class, 'deactivate'])->name('organizaciones.deactivate');
Route::get('/organizaciones/exportar', [OrganizacionesController::class, 'exportar'])->name('organizaciones.exportar');
Route::delete('/organizaciones/bulk-delete', [OrganizacionesController::class, 'bulkDelete'])->name('organizaciones.bulk-delete');

// Ruta de debug para probar el token
Route::get('/debug-vote', function(Request $request) {
    $token = $request->query('token');
    return response()->json([
        'token' => $token,
        'token_type' => gettype($token),
        'is_array' => is_array($token),
        'url' => $request->fullUrl()
    ]);
});


// Ruta temporal para probar estilos (sin auth)
Route::get('/test-styles', function () {
    return view('miembros.create', [
        'organizaciones' => \App\Models\Organizacion::activas()->get(),
        'estadosMembresia' => \App\Models\EstadoMembresia::all()
    ]);
});


// Ruta temporal para probar dashboard (sin auth)
Route::get('/test-dashboard', [DashboardController::class, 'index']);


// API pública para verificar autenticación (sin middleware auth)
Route::get('/api/auth/check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->check() ? [
            'id' => auth()->id(),
            'name' => auth()->user()->name ?? 'Usuario',
            'email' => auth()->user()->email ?? ''
        ] : null
    ]);
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API para estadísticas en tiempo real
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/api/dashboard/graficos', [DashboardController::class, 'datosGraficos'])->name('dashboard.graficos');
    
    // API para organizaciones
    Route::get('/api/organizaciones', [OrganizacionesController::class, 'api'])->name('organizaciones.api');
    Route::get('/api/organizaciones/estadisticas', [OrganizacionesController::class, 'estadisticas'])->name('organizaciones.estadisticas');
    Route::get('/api/organizaciones/buscar', [OrganizacionesController::class, 'buscar'])->name('organizaciones.buscar');
    
    // Módulo Miembros
    Route::get('/miembros', [MiembrosController::class, 'index'])->name('miembros.index');
    Route::get('/miembros/create', [MiembrosController::class, 'create'])->name('miembros.create');
    Route::post('/miembros', [MiembrosController::class, 'store'])->name('miembros.store');
    Route::get('/miembros/{id}/profile', [MiembrosController::class, 'profile'])->name('miembros.profile');
    Route::get('/miembros/{id}/edit', [MiembrosController::class, 'edit'])->name('miembros.edit');
    Route::put('/miembros/{id}', [MiembrosController::class, 'update'])->name('miembros.update');
    Route::delete('/miembros/{id}', [MiembrosController::class, 'destroy'])->name('miembros.destroy');
    
    // Rutas para documentación de miembros
    Route::post('/miembros/{id}/documentation/upload', [MiembrosController::class, 'uploadDocument'])->name('miembros.documentation.upload');
    Route::get('/miembros/{id}/documentation', [MiembrosController::class, 'getDocuments'])->name('miembros.documentation.index');
    Route::put('/miembros/{id}/documentation/{document}/rename', [MiembrosController::class, 'renameDocument'])->name('miembros.documentation.rename');
    Route::delete('/miembros/{id}/documentation/{document}', [MiembrosController::class, 'deleteDocument'])->name('miembros.documentation.delete');
    Route::get('/miembros/{id}/carnet', [MiembrosController::class, 'carnet'])->name('miembros.carnet');
    Route::get('/miembros/{id}/carnet-data', [MiembrosController::class, 'carnetData'])->name('miembros.carnet-data');
    Route::get('/miembros/exportar', [MiembrosController::class, 'exportar'])->name('miembros.exportar');
    Route::delete('/miembros/bulk-delete', [MiembrosController::class, 'bulkDelete'])->name('miembros.bulk-delete');
    
    // Módulo Organizaciones (temporalmente movido fuera del middleware)
    
    // Módulo Cuotas
    Route::get('/cuotas', [App\Http\Controllers\CuotasController::class, 'index'])->name('cuotas.index');
    Route::get('/cuotas/reportes', [App\Http\Controllers\CuotasController::class, 'reportes'])->name('cuotas.reportes');
    Route::get('/cuotas/create', [App\Http\Controllers\CuotasController::class, 'create'])->name('cuotas.create');
    Route::post('/cuotas', [App\Http\Controllers\CuotasController::class, 'store'])->name('cuotas.store');
    Route::get('/cuotas/{cuota}', [App\Http\Controllers\CuotasController::class, 'show'])->name('cuotas.show');
    Route::get('/cuotas/{cuota}/edit', [App\Http\Controllers\CuotasController::class, 'edit'])->name('cuotas.edit');
    Route::put('/cuotas/{cuota}', [App\Http\Controllers\CuotasController::class, 'update'])->name('cuotas.update');
    Route::delete('/cuotas/{cuota}', [App\Http\Controllers\CuotasController::class, 'destroy'])->name('cuotas.destroy');
    Route::post('/cuotas/{cuota}/marcar-pagada', [App\Http\Controllers\CuotasController::class, 'marcarPagada'])->name('cuotas.marcar-pagada');
    Route::post('/cuotas/generar', [App\Http\Controllers\CuotasController::class, 'generarCuotas'])->name('cuotas.generar');
    Route::post('/cuotas/actualizar-vencidas', [App\Http\Controllers\CuotasController::class, 'actualizarVencidas'])->name('cuotas.actualizar-vencidas');
    Route::get('/cuotas/exportar', [App\Http\Controllers\CuotasController::class, 'exportar'])->name('cuotas.exportar');
    Route::delete('/cuotas/bulk-delete', [App\Http\Controllers\CuotasController::class, 'bulkDelete'])->name('cuotas.bulk-delete');
    
    // Módulo Directiva
    Route::get('/directivas', [DirectivaController::class, 'index'])->name('directivas.index');
    Route::get('/directivas/create', [DirectivaController::class, 'create'])->name('directivas.create');
    Route::post('/directivas', [DirectivaController::class, 'store'])->name('directivas.store');
    Route::get('/directivas/{directiva}', [DirectivaController::class, 'show'])->name('directivas.show');
    Route::get('/directivas/{directiva}/edit', [DirectivaController::class, 'edit'])->name('directivas.edit');
    Route::put('/directivas/{directiva}', [DirectivaController::class, 'update'])->name('directivas.update');
    Route::delete('/directivas/{directiva}', [DirectivaController::class, 'destroy'])->name('directivas.destroy');
    Route::delete('/directivas/bulk-delete', [DirectivaController::class, 'bulkDelete'])->name('directivas.bulk-delete');
    Route::post('/directivas/{directiva}/activate', [DirectivaController::class, 'activate'])->name('directivas.activate');
    Route::post('/directivas/{directiva}/deactivate', [DirectivaController::class, 'deactivate'])->name('directivas.deactivate');
    Route::post('/directivas/{directiva}/suspend', [DirectivaController::class, 'suspend'])->name('directivas.suspend');
    Route::post('/directivas/{directiva}/finish', [DirectivaController::class, 'finish'])->name('directivas.finish');
    Route::post('/directivas/{directiva}/renew', [DirectivaController::class, 'renew'])->name('directivas.renew');
    Route::get('/directivas/organo/{organo}', [DirectivaController::class, 'porOrgano'])->name('directivas.por-organo');
    Route::get('/directivas/cargo/{cargo}', [DirectivaController::class, 'porCargo'])->name('directivas.por-cargo');
    Route::get('/directivas/activas', [DirectivaController::class, 'activas'])->name('directivas.activas');
    Route::get('/directivas/proximos-vencimientos', [DirectivaController::class, 'proximosVencimientos'])->name('directivas.proximos-vencimientos');
    Route::get('/directivas/exportar', [DirectivaController::class, 'export'])->name('directivas.exportar');
    
    // Rutas legacy de directiva (mantener compatibilidad)
    Route::get('/directiva', [DirectivaController::class, 'index'])->name('directiva.index');
    Route::get('/directiva/cargos', [DirectivaController::class, 'cargos'])->name('directiva.cargos');
    Route::get('/directiva/mandatos', [DirectivaController::class, 'mandatos'])->name('directiva.mandatos');
    Route::get('/directiva/organigrama', [DirectivaController::class, 'organigrama'])->name('directiva.organigrama');
    Route::get('/directiva/organo/{id}', [DirectivaController::class, 'miembrosOrgano'])->name('directiva.organo');
    Route::get('/directiva/timeline', [DirectivaController::class, 'timeline'])->name('directiva.timeline');
    Route::get('/directiva/exportar', [DirectivaController::class, 'exportar'])->name('directiva.exportar');
    
    // Módulo Asambleas
    Route::get('/asambleas', function () {
        return view('asambleas.index');
    })->name('asambleas.index');
    Route::get('/asambleas/create', function () {
        return view('asambleas.create');
    })->name('asambleas.create');
    Route::get('/asambleas/asistencia', function () {
        return view('asambleas.asistencia');
    })->name('asambleas.asistencia');
    
    // Módulo Elecciones
    // Tipos de Elecciones
    Route::resource('tipos-elecciones', App\Http\Controllers\TipoEleccionController::class, [
        'parameters' => ['tipos-elecciones' => 'tipoEleccion']
    ])->middleware('auth');
    
    // Elecciones
    Route::get('/elecciones', [App\Http\Controllers\EleccionController::class, 'index'])->name('elecciones.index');
    Route::get('/elecciones/crear', [App\Http\Controllers\EleccionAdminController::class, 'create'])->name('elecciones.create')->middleware('auth');
    Route::post('/elecciones', [App\Http\Controllers\EleccionAdminController::class, 'store'])->name('elecciones.store')->middleware('auth');
    Route::get('/elecciones/{eleccion}/editar', [App\Http\Controllers\EleccionAdminController::class, 'edit'])->name('elecciones.edit')->middleware('auth');
    Route::put('/elecciones/{eleccion}', [App\Http\Controllers\EleccionAdminController::class, 'update'])->name('elecciones.update')->middleware('auth');
    Route::delete('/elecciones/{eleccion}', [App\Http\Controllers\EleccionAdminController::class, 'destroy'])->name('elecciones.destroy')->middleware('auth');
    Route::get('/elecciones/candidatos', [App\Http\Controllers\EleccionController::class, 'candidatos'])->name('elecciones.candidatos');
    Route::get('/elecciones/votacion', [App\Http\Controllers\EleccionController::class, 'votacion'])->name('elecciones.votacion');
    Route::get('/elecciones/{id}/resultados', [App\Http\Controllers\EleccionController::class, 'results'])->name('elecciones.resultados');
    Route::get('/elecciones/{id}/verificar-estado', [App\Http\Controllers\EleccionController::class, 'verificarEstado'])->name('elecciones.verificar-estado');
});

// Rutas públicas (sin autenticación ni CSRF)
Route::get('/votar/{eleccion}', [App\Http\Controllers\VotacionPublicaController::class, 'show'])->name('votacion.publica');
Route::post('/votar/{eleccion}', [App\Http\Controllers\VotacionPublicaController::class, 'votar'])->name('votacion.publica.submit')->withoutMiddleware(['web']);

// Rutas para tokens de votación
Route::get('/api/elecciones/{eleccion}/generar-token-publico', [App\Http\Controllers\VotingTokenController::class, 'generarTokenPublico'])->name('api.generar-token-publico');
Route::post('/api/elecciones/{eleccion}/generar-token-privado', [App\Http\Controllers\VotingTokenController::class, 'generarTokenPrivado'])->name('api.generar-token-privado');
Route::get('/api/elecciones/{eleccion}/validar-token/{token}', [App\Http\Controllers\VotingTokenController::class, 'validarToken'])->name('api.validar-token');
    
    // Votación (protegido con auth y CSRF)
    Route::post('/votos', [App\Http\Controllers\VotoController::class, 'store'])->name('votos.store');
    Route::get('/votos/verificar/{eleccion}', [App\Http\Controllers\VotoController::class, 'verificarVoto'])->name('votos.verificar');
    Route::get('/votos/mi-voto/{eleccion}', [App\Http\Controllers\VotoController::class, 'miVoto'])->name('votos.mi-voto');

    // Sistema de Links Seguros de Votación (Tokens JWT) - Admin
    Route::get('/elecciones/{eleccion}/generar-links', [App\Http\Controllers\VotingLinkController::class, 'index'])
        ->name('voting.generar-links');
    Route::post('/elecciones/{eleccion}/generar-link-eleccion', [App\Http\Controllers\VotingLinkController::class, 'generarLinkEleccion'])
        ->name('voting.generar-link-eleccion');
    Route::post('/elecciones/{eleccion}/generar-token', [App\Http\Controllers\VotingLinkController::class, 'generarToken'])
        ->name('voting.generar-token');
    Route::post('/elecciones/{eleccion}/generar-tokens-masivos', [App\Http\Controllers\VotingLinkController::class, 'generarTokensMasivos'])
        ->name('voting.generar-tokens-masivos');
    
    // Módulo Formación
    Route::get('/cursos', function () {
        return view('cursos.index');
    })->name('cursos.index');
    Route::get('/cursos/inscripciones', function () {
        return view('cursos.inscripciones');
    })->name('cursos.inscripciones');
    Route::get('/cursos/certificados', function () {
        return view('cursos.certificados');
    })->name('cursos.certificados');
    
    // Módulo Reportes
    Route::get('/reportes/miembros', function () {
        return view('reportes.miembros');
    })->name('reportes.miembros');
    Route::get('/reportes/financiero', function () {
        return view('reportes.financiero');
    })->name('reportes.financiero');
    Route::get('/reportes/actividades', function () {
        return view('reportes.actividades');
    })->name('reportes.actividades');
    
    // Módulo Transparencia
    Route::get('/documentos', function () {
        return view('documentos.index');
    })->name('documentos.index');
    Route::get('/documentos/actas', function () {
        return view('documentos.actas');
    })->name('documentos.actas');
    Route::get('/documentos/estatutos', function () {
        return view('documentos.estatutos');
    })->name('documentos.estatutos');
    
    // Módulo Configuración
    Route::get('/usuarios', function () {
        return view('usuarios.index');
    })->name('usuarios.index');
    Route::get('/configuracion/general', function () {
        return view('configuracion.general');
    })->name('configuracion.general');
    
    // Módulo Roles y Permisos
    Route::resource('roles', \App\Http\Controllers\RolesController::class);
    Route::resource('permisos', \App\Http\Controllers\PermisosController::class);
    
    // Módulo Soporte - Gestión de Membresías
    Route::get('/soporte/membresias', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'index'])->name('soporte.membresias.index');
    Route::post('/soporte/membresias/estado', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'storeEstado'])->name('soporte.membresias.estado.store');
    Route::put('/soporte/membresias/estado/{id}', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'updateEstado'])->name('soporte.membresias.estado.update');
    Route::delete('/soporte/membresias/estado/{id}', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'destroyEstado'])->name('soporte.membresias.estado.destroy');
    Route::post('/soporte/membresias/tipo', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'storeTipo'])->name('soporte.membresias.tipo.store');
    Route::put('/soporte/membresias/tipo/{id}', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'updateTipo'])->name('soporte.membresias.tipo.update');
    Route::delete('/soporte/membresias/tipo/{id}', [\App\Http\Controllers\Soporte\SoporteMembresiasController::class, 'destroyTipo'])->name('soporte.membresias.tipo.destroy');
    
// Módulo Noticias
Route::get('/noticias', [App\Http\Controllers\NoticiasController::class, 'index'])->name('noticias.index');
Route::get('/noticias/create', [App\Http\Controllers\NoticiasController::class, 'create'])->name('noticias.create');
Route::post('/noticias', [App\Http\Controllers\NoticiasController::class, 'store'])->name('noticias.store');
Route::get('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'show'])->name('noticias.show');
Route::get('/noticias/{id}/edit', [App\Http\Controllers\NoticiasController::class, 'edit'])->name('noticias.edit');
Route::put('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'update'])->name('noticias.update');
Route::delete('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'destroy'])->name('noticias.destroy');

// Módulo Directiva
Route::get('/directiva', [App\Http\Controllers\DirectivaController::class, 'index'])->name('directiva.index');
Route::get('/directiva/create', [App\Http\Controllers\DirectivaController::class, 'create'])->name('directiva.create');
Route::post('/directiva', [App\Http\Controllers\DirectivaController::class, 'store'])->name('directiva.store');
Route::get('/directiva/{id}', [App\Http\Controllers\DirectivaController::class, 'show'])->name('directiva.show');
Route::get('/directiva/{id}/edit', [App\Http\Controllers\DirectivaController::class, 'edit'])->name('directiva.edit');
Route::put('/directiva/{id}', [App\Http\Controllers\DirectivaController::class, 'update'])->name('directiva.update');
Route::delete('/directiva/{id}', [App\Http\Controllers\DirectivaController::class, 'destroy'])->name('directiva.destroy');

// Módulo Asambleas
Route::get('/asambleas', [AsambleaController::class, 'index'])->name('asambleas.index');
Route::get('/asambleas/proxima', [AsambleaController::class, 'proxima'])->name('asambleas.proxima');
Route::get('/asambleas/create', [AsambleaController::class, 'create'])->name('asambleas.create');
Route::post('/asambleas', [AsambleaController::class, 'store'])->name('asambleas.store');
Route::post('/asambleas/confirmar-asistencia', [AsambleaController::class, 'confirmarAsistencia'])->name('asambleas.confirmar-asistencia');

// Módulo Asistencias de Asambleas (DEBE IR ANTES de las rutas con {asamblea} para evitar conflictos)
Route::get('/asambleas/asistencias', [App\Http\Controllers\AsistenciaAsambleaController::class, 'index'])->name('asambleas.asistencias.index');
Route::get('/asambleas/asistencias/create', [App\Http\Controllers\AsistenciaAsambleaController::class, 'create'])->name('asambleas.asistencias.create');
Route::post('/asambleas/asistencias', [App\Http\Controllers\AsistenciaAsambleaController::class, 'store'])->name('asambleas.asistencias.store');
Route::post('/asambleas/asistencias/confirmar', [App\Http\Controllers\AsistenciaAsambleaController::class, 'confirmarAsistencia'])->name('asambleas.asistencias.confirmar');
Route::get('/asambleas/asistencias/{asistenciaAsamblea}', [App\Http\Controllers\AsistenciaAsambleaController::class, 'show'])->name('asambleas.asistencias.show');
Route::get('/asambleas/asistencias/{asistenciaAsamblea}/edit', [App\Http\Controllers\AsistenciaAsambleaController::class, 'edit'])->name('asambleas.asistencias.edit');
Route::put('/asambleas/asistencias/{asistenciaAsamblea}', [App\Http\Controllers\AsistenciaAsambleaController::class, 'update'])->name('asambleas.asistencias.update');
Route::delete('/asambleas/asistencias/{asistenciaAsamblea}', [App\Http\Controllers\AsistenciaAsambleaController::class, 'destroy'])->name('asambleas.asistencias.destroy');
Route::post('/asambleas/asistencias/{asistenciaAsamblea}/presente', [App\Http\Controllers\AsistenciaAsambleaController::class, 'marcarPresente'])->name('asambleas.asistencias.presente');
Route::post('/asambleas/asistencias/{asistenciaAsamblea}/ausente', [App\Http\Controllers\AsistenciaAsambleaController::class, 'marcarAusente'])->name('asambleas.asistencias.ausente');
Route::post('/asambleas/asistencias/{asistenciaAsamblea}/tardanza', [App\Http\Controllers\AsistenciaAsambleaController::class, 'marcarTardanza'])->name('asambleas.asistencias.tardanza');

// Rutas de Asambleas con parámetro {asamblea} (DEBEN IR DESPUÉS de las rutas específicas)
Route::get('/asambleas/{asamblea}', [AsambleaController::class, 'show'])->name('asambleas.show');
Route::get('/asambleas/{asamblea}/edit', [AsambleaController::class, 'edit'])->name('asambleas.edit');
Route::put('/asambleas/{asamblea}', [AsambleaController::class, 'update'])->name('asambleas.update');
Route::delete('/asambleas/{asamblea}', [AsambleaController::class, 'destroy'])->name('asambleas.destroy');

// Módulo Capacitaciones
Route::get('/capacitaciones', [CapacitacionController::class, 'index'])->name('capacitaciones.index');
Route::get('/capacitaciones/proximo', [CapacitacionController::class, 'proximo'])->name('capacitaciones.proximo');
Route::get('/capacitaciones/inscripciones', [CapacitacionController::class, 'inscripciones'])->name('capacitaciones.inscripciones');
Route::get('/capacitaciones/create', [CapacitacionController::class, 'create'])->name('capacitaciones.create');
Route::post('/capacitaciones', [CapacitacionController::class, 'store'])->name('capacitaciones.store');
Route::get('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'show'])->name('capacitaciones.show');
Route::get('/capacitaciones/{capacitacion}/edit', [CapacitacionController::class, 'edit'])->name('capacitaciones.edit');
Route::put('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'update'])->name('capacitaciones.update');
Route::delete('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'destroy'])->name('capacitaciones.destroy');
Route::post('/capacitaciones/inscribir', [CapacitacionController::class, 'inscribir'])->name('capacitaciones.inscribir');
    
    // Módulo Cronograma Directiva
    Route::resource('cronograma-directiva', CronogramaDirectivaController::class);
    Route::delete('/cronograma-directiva/bulk-delete', [CronogramaDirectivaController::class, 'bulkDelete'])->name('cronograma-directiva.bulk-delete');
    Route::post('/cronograma-directiva/{cronogramaDirectiva}/iniciar', [CronogramaDirectivaController::class, 'iniciar'])->name('cronograma-directiva.iniciar');
    Route::post('/cronograma-directiva/{cronogramaDirectiva}/completar', [CronogramaDirectivaController::class, 'completar'])->name('cronograma-directiva.completar');
    Route::post('/cronograma-directiva/{cronogramaDirectiva}/cancelar', [CronogramaDirectivaController::class, 'cancelar'])->name('cronograma-directiva.cancelar');
    Route::get('/cronograma-directiva/exportar', [CronogramaDirectivaController::class, 'export'])->name('cronograma-directiva.exportar');

    // Rutas del sistema de carnet
Route::middleware(['auth'])->prefix('carnet')->name('carnet.')->group(function () {
    Route::get('/{miembro}/selector', [CarnetController::class, 'selector'])->name('selector');
    Route::get('/{miembro}/editor/{template}', [CarnetController::class, 'editor'])->name('editor');
    Route::post('/{miembro}/personalizar', [CarnetController::class, 'guardarPersonalizacion'])->name('personalizar');
    Route::get('/{miembro}/generar/{template}', [CarnetController::class, 'generar'])->name('generar');
    Route::post('/{miembro}/subir-foto', [CarnetController::class, 'subirFoto'])->name('subir-foto');
    Route::get('/{miembro}/pdf/{template}', [CarnetController::class, 'generarPDF'])->name('pdf');
});