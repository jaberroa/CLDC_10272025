@extends('partials.layouts.master')

@section('title', 'Votación | CLDCI')
@section('title-sub', 'Sistema de Votación')
@section('pagetitle', 'Votación')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .votacion-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }
    
    .candidato-votacion {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    
    .candidato-votacion:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .candidato-votacion.seleccionado {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .candidato-votacion.seleccionado::before {
        content: "✓";
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .votar-final-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 1rem 3rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s;
    }
    
    .votar-final-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        color: white;
    }
</style>
@endsection

@section('content')
<!-- Header de Votación -->
<div class="votacion-header text-center">
    <h2 class="mb-2">
        <i class="ri-checkbox-circle-line me-2"></i>
        Elección Directiva 2024
    </h2>
    <p class="mb-3 opacity-90">Selecciona tu candidato preferido para cada cargo</p>
    <div class="d-flex justify-content-center gap-4">
        <div>
            <i class="ri-calendar-line me-1"></i>
            Cierra: 31/12/2024 23:59
        </div>
        <div>
            <i class="ri-user-line me-1"></i>
            Votantes: 248/320
        </div>
    </div>
</div>

<!-- Instrucciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5 class="alert-heading">
                <i class="ri-information-line me-2"></i>
                Instrucciones de Votación
            </h5>
            <ol class="mb-0">
                <li>Selecciona un candidato por cada cargo haciendo clic en su tarjeta</li>
                <li>Puedes cambiar tu selección haciendo clic en otro candidato</li>
                <li>Revisa tus selecciones en el resumen al final de la página</li>
                <li>Presiona "Confirmar Voto" para registrar tu voto (esta acción no se puede deshacer)</li>
            </ol>
        </div>
    </div>
</div>

<!-- Votación por Cargo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-soft-primary">
                <h5 class="mb-0">
                    <i class="ri-shield-star-line me-2"></i>
                    Presidente
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="col-md-4">
                        <div class="candidato-votacion" onclick="seleccionarCandidato('presidente', {{ $i }})">
                            <div class="text-center mb-3">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                                    {{ chr(64 + $i) }}
                                </div>
                            </div>
                            <h6 class="text-center mb-2">Candidato Presidente {{ $i }}</h6>
                            <p class="text-muted small text-center mb-0">
                                Propuesta enfocada en desarrollo y modernización
                            </p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-soft-success">
                <h5 class="mb-0">
                    <i class="ri-user-star-line me-2"></i>
                    Vicepresidente
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @for($i = 1; $i <= 2; $i++)
                    <div class="col-md-6">
                        <div class="candidato-votacion" onclick="seleccionarCandidato('vicepresidente', {{ $i }})">
                            <div class="text-center mb-3">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #198754 0%, #20c997 100%); margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                                    V{{ $i }}
                                </div>
                            </div>
                            <h6 class="text-center mb-2">Candidato Vicepresidente {{ $i }}</h6>
                            <p class="text-muted small text-center mb-0">
                                Experiencia en coordinación y gestión de proyectos
                            </p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen de Votación -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="ri-file-list-line me-2"></i>
                    Resumen de tu Voto
                </h5>
            </div>
            <div class="card-body">
                <div id="resumenVotacion" class="text-muted text-center py-4">
                    <i class="ri-checkbox-line display-4 mb-3"></i>
                    <p>Aún no has seleccionado candidatos. Por favor, selecciona tus opciones arriba.</p>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button class="votar-final-btn" id="btnConfirmarVoto" disabled onclick="confirmarVoto()">
                        <i class="ri-checkbox-circle-line me-2"></i>
                        Confirmar Voto
                    </button>
                    <button class="btn btn-soft-secondary" onclick="limpiarSelecciones()">
                        <i class="ri-refresh-line me-1"></i>
                        Limpiar Selecciones
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let votosSeleccionados = {};
    
    function seleccionarCandidato(cargo, candidatoId) {
        // Remover selección previa del mismo cargo
        document.querySelectorAll(`[onclick*="${cargo}"]`).forEach(el => {
            el.classList.remove('seleccionado');
        });
        
        // Agregar nueva selección
        event.currentTarget.classList.add('seleccionado');
        votosSeleccionados[cargo] = candidatoId;
        
        actualizarResumen();
    }
    
    function actualizarResumen() {
        const resumen = document.getElementById('resumenVotacion');
        const btnConfirmar = document.getElementById('btnConfirmarVoto');
        
        if (Object.keys(votosSeleccionados).length === 0) {
            resumen.innerHTML = `
                <i class="ri-checkbox-line display-4 mb-3 text-muted"></i>
                <p class="text-muted">Aún no has seleccionado candidatos.</p>
            `;
            btnConfirmar.disabled = true;
            return;
        }
        
        let html = '<div class="text-start">';
        for (let cargo in votosSeleccionados) {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                    <div>
                        <strong>${cargo.charAt(0).toUpperCase() + cargo.slice(1)}</strong><br>
                        <small class="text-muted">Candidato ${votosSeleccionados[cargo]}</small>
                    </div>
                    <i class="ri-checkbox-circle-fill text-success fs-4"></i>
                </div>
            `;
        }
        html += '</div>';
        
        resumen.innerHTML = html;
        btnConfirmar.disabled = false;
    }
    
    function limpiarSelecciones() {
        document.querySelectorAll('.candidato-votacion').forEach(el => {
            el.classList.remove('seleccionado');
        });
        votosSeleccionados = {};
        actualizarResumen();
        showInfoToast('Selecciones limpiadas');
    }
    
    function confirmarVoto() {
        if (Object.keys(votosSeleccionados).length === 0) {
            Swal.fire({
                title: 'Atención',
                text: 'Debes seleccionar al menos un candidato',
                icon: 'warning',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Entendido',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        Swal.fire({
            title: '¿Confirmar voto?',
            html: '<p class="mb-3">¿Estás seguro de confirmar tu voto?</p><strong class="text-danger">Esta acción no se puede deshacer</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ri-checkbox-circle-line me-1"></i> Sí, confirmar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para enviar el voto al servidor
                
                Swal.fire({
                    title: '¡Voto Registrado!',
                    html: '<p class="mb-2">Tu voto ha sido registrado exitosamente</p><small class="text-muted">Gracias por participar en el proceso electoral</small>',
                    icon: 'success',
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'Finalizar',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                
                // Deshabilitar votación
                document.querySelectorAll('.candidato-votacion').forEach(el => {
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.6';
                });
                
                document.getElementById('btnConfirmarVoto').disabled = true;
                document.getElementById('btnConfirmarVoto').innerHTML = '<i class="ri-check-line me-2"></i> Voto Registrado';
            }
        });
    }
</script>
@endsection

