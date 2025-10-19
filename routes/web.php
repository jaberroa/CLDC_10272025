<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MiembrosController;
use App\Http\Controllers\DirectivaController;

// Cargar rutas de autenticación
require_once __DIR__ . '/auth.php';

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

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
    
    // Módulo Miembros
    Route::get('/miembros', [MiembrosController::class, 'index'])->name('miembros.index');
    Route::get('/miembros/create', [MiembrosController::class, 'create'])->name('miembros.create');
    Route::post('/miembros', [MiembrosController::class, 'store'])->name('miembros.store');
    Route::get('/miembros/{id}', [MiembrosController::class, 'show'])->name('miembros.show');
    Route::get('/miembros/{id}/edit', [MiembrosController::class, 'edit'])->name('miembros.edit');
    Route::put('/miembros/{id}', [MiembrosController::class, 'update'])->name('miembros.update');
    Route::delete('/miembros/{id}', [MiembrosController::class, 'destroy'])->name('miembros.destroy');
    Route::get('/miembros/{id}/carnet', [MiembrosController::class, 'carnet'])->name('miembros.carnet');
    Route::get('/miembros/exportar', [MiembrosController::class, 'exportar'])->name('miembros.exportar');
    
    // Módulo Directiva
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
    Route::get('/elecciones', function () {
        return view('elecciones.index');
    })->name('elecciones.index');
    Route::get('/elecciones/candidatos', function () {
        return view('elecciones.candidatos');
    })->name('elecciones.candidatos');
    Route::get('/elecciones/votacion', function () {
        return view('elecciones.votacion');
    })->name('elecciones.votacion');
    
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
    Route::get('/organizaciones', function () {
        return view('organizaciones.index');
    })->name('organizaciones.index');
    Route::get('/usuarios', function () {
        return view('usuarios.index');
    })->name('usuarios.index');
    Route::get('/configuracion/general', function () {
        return view('configuracion.general');
    })->name('configuracion.general');
    
        // Módulo Noticias
        Route::get('/noticias', [App\Http\Controllers\NoticiasController::class, 'index'])->name('noticias.index');
        Route::get('/noticias/create', [App\Http\Controllers\NoticiasController::class, 'create'])->name('noticias.create');
        Route::post('/noticias', [App\Http\Controllers\NoticiasController::class, 'store'])->name('noticias.store');
        Route::get('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'show'])->name('noticias.show');
        Route::get('/noticias/{id}/edit', [App\Http\Controllers\NoticiasController::class, 'edit'])->name('noticias.edit');
        Route::put('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'update'])->name('noticias.update');
        Route::delete('/noticias/{id}', [App\Http\Controllers\NoticiasController::class, 'destroy'])->name('noticias.destroy');
        
        // Perfil de usuario
        Route::get('/profile', function () {
            return view('profile.edit');
        })->name('profile.edit');
});