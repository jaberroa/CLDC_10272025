<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestionDocumental\SeccionesDocumentalesController;
use App\Http\Controllers\GestionDocumental\CarpetasDocumentalesController;
use App\Http\Controllers\GestionDocumental\DocumentosGestionController;
use App\Http\Controllers\GestionDocumental\ComparticionController;
use App\Http\Controllers\GestionDocumental\AprobacionesController;
use App\Http\Controllers\GestionDocumental\FirmasController;
use App\Http\Controllers\GestionDocumental\BusquedaController;
use App\Http\Controllers\GestionDocumental\ExploradorController;

/*
|--------------------------------------------------------------------------
| Rutas de Gestión Documental
|--------------------------------------------------------------------------
*/

Route::prefix('gestion-documental')->name('gestion-documental.')->middleware(['auth'])->group(function () {
    
    // Dashboard (ahora redirige al explorador)
    Route::get('/', function () {
        return redirect()->route('gestion-documental.explorador.index');
    })->name('dashboard')->middleware('documental.permission:documentos.ver');
    
    // Explorador (estilo Google Drive)
    Route::get('/explorador', [ExploradorController::class, 'index'])
        ->name('explorador.index')
        ->middleware('documental.permission:documentos.ver');
    Route::post('/explorador/crear-carpeta', [ExploradorController::class, 'crearCarpeta'])
        ->name('explorador.crear-carpeta')
        ->middleware('documental.permission:carpetas.gestionar');
    Route::post('/explorador/subir-archivo', [ExploradorController::class, 'subirArchivo'])
        ->name('explorador.subir-archivo')
        ->middleware('documental.permission:documentos.crear');
    Route::post('/explorador/mover', [ExploradorController::class, 'mover'])
        ->name('explorador.mover')
        ->middleware('documental.permission:documentos.editar');
    Route::post('/explorador/renombrar', [ExploradorController::class, 'renombrar'])
        ->name('explorador.renombrar')
        ->middleware('documental.permission:documentos.editar');
    Route::post('/explorador/eliminar', [ExploradorController::class, 'eliminar'])
        ->name('explorador.eliminar')
        ->middleware('documental.permission:documentos.eliminar');
    Route::get('/explorador/detalles', [ExploradorController::class, 'detalles'])
        ->name('explorador.detalles')
        ->middleware('documental.permission:documentos.ver');

    // Secciones
    Route::resource('secciones', SeccionesDocumentalesController::class)
        ->parameters(['secciones' => 'seccion'])
        ->middleware('documental.permission:secciones.gestionar');
    Route::post('secciones/{seccion}/toggle-activa', [SeccionesDocumentalesController::class, 'toggleActiva'])
        ->name('secciones.toggle-activa')
        ->middleware('documental.permission:secciones.gestionar');
    Route::post('secciones/reordenar', [SeccionesDocumentalesController::class, 'reordenar'])
        ->name('secciones.reordenar')
        ->middleware('documental.permission:secciones.gestionar');

    // Carpetas
    Route::resource('carpetas', CarpetasDocumentalesController::class)
        ->parameters(['carpetas' => 'carpeta'])
        ->middleware('documental.permission:carpetas.gestionar');
    Route::post('carpetas/{carpeta}/mover', [CarpetasDocumentalesController::class, 'mover'])
        ->name('carpetas.mover')
        ->middleware('documental.permission:carpetas.gestionar');
    Route::get('carpetas/arbol/json', [CarpetasDocumentalesController::class, 'arbol'])
        ->name('carpetas.arbol')
        ->middleware('documental.permission:documentos.ver');

    // Documentos
    Route::get('documentos', [DocumentosGestionController::class, 'index'])
        ->name('documentos.index')
        ->middleware('documental.permission:documentos.ver');
    Route::get('documentos/create', [DocumentosGestionController::class, 'create'])
        ->name('documentos.create')
        ->middleware('documental.permission:documentos.crear');
    Route::post('documentos', [DocumentosGestionController::class, 'store'])
        ->name('documentos.store')
        ->middleware('documental.permission:documentos.crear');
    Route::get('documentos/{documento}', [DocumentosGestionController::class, 'show'])
        ->name('documentos.show')
        ->middleware('documental.permission:documentos.ver');
    Route::get('documentos/{documento}/edit', [DocumentosGestionController::class, 'edit'])
        ->name('documentos.edit')
        ->middleware('documental.permission:documentos.editar');
    Route::put('documentos/{documento}', [DocumentosGestionController::class, 'update'])
        ->name('documentos.update')
        ->middleware('documental.permission:documentos.editar');
    Route::delete('documentos/{documento}', [DocumentosGestionController::class, 'destroy'])
        ->name('documentos.destroy')
        ->middleware('documental.permission:documentos.eliminar');
    Route::get('documentos/{documento}/descargar', [DocumentosGestionController::class, 'descargar'])
        ->name('documentos.descargar')
        ->middleware('documental.permission:documentos.ver');
    Route::get('documentos/{documento}/preview', [DocumentosGestionController::class, 'preview'])
        ->name('documentos.preview')
        ->middleware('documental.permission:documentos.ver');
    Route::post('documentos/{documento}/duplicar', [DocumentosGestionController::class, 'duplicar'])
        ->name('documentos.duplicar')
        ->middleware('documental.permission:documentos.crear');
    Route::post('documentos/{documento}/mover', [DocumentosGestionController::class, 'mover'])
        ->name('documentos.mover')
        ->middleware('documental.permission:documentos.editar');

    // Compartición
    Route::post('documentos/{documento}/compartir', [ComparticionController::class, 'compartir'])
        ->name('comparticion.compartir')
        ->middleware('documental.permission:documentos.compartir');
    Route::post('comparticion/{comparticion}/revocar', [ComparticionController::class, 'revocar'])
        ->name('comparticion.revocar')
        ->middleware('documental.permission:documentos.compartir');

    // Aprobaciones
    Route::get('aprobaciones/mis-pendientes', [AprobacionesController::class, 'misPendientes'])
        ->name('aprobaciones.mis-pendientes')
        ->middleware('documental.permission:documentos.ver');
    Route::post('aprobaciones/{aprobacion}/aprobar', [AprobacionesController::class, 'aprobar'])
        ->name('aprobaciones.aprobar')
        ->middleware('documental.permission:documentos.aprobar');
    Route::post('aprobaciones/{aprobacion}/rechazar', [AprobacionesController::class, 'rechazar'])
        ->name('aprobaciones.rechazar')
        ->middleware('documental.permission:documentos.aprobar');
    Route::get('documentos/{documento}/aprobaciones/historial', [AprobacionesController::class, 'historial'])
        ->name('aprobaciones.historial')
        ->middleware('documental.permission:documentos.ver');

    // Firmas
    Route::post('documentos/{documento}/solicitar-firma', [FirmasController::class, 'solicitar'])
        ->name('firmas.solicitar')
        ->middleware('documental.permission:documentos.editar');
    Route::get('firmas/mis-pendientes', [FirmasController::class, 'misPendientes'])
        ->name('firmas.mis-pendientes')
        ->middleware('documental.permission:documentos.ver');

    // Búsqueda
    Route::get('busqueda', [BusquedaController::class, 'index'])
        ->name('busqueda.index')
        ->middleware('documental.permission:documentos.ver');
    Route::get('busqueda/api', [BusquedaController::class, 'api'])
        ->name('busqueda.api')
        ->middleware('documental.permission:documentos.ver');
});

// Rutas públicas (sin autenticación)
Route::prefix('documentos')->name('documentos.')->group(function () {
    // Compartición pública
    Route::get('compartido/{token}', [ComparticionController::class, 'verCompartido'])
        ->name('compartido');
    Route::post('compartido/{token}/verificar-password', [ComparticionController::class, 'verificarPassword'])
        ->name('compartido.verificar-password');

    // Firmas externas
    Route::get('firmar/{token}', [FirmasController::class, 'verFirma'])
        ->name('firmar');
    Route::post('firmar/{token}', [FirmasController::class, 'firmar'])
        ->name('firmar.guardar');
});

