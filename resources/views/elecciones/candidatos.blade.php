@extends('partials.layouts.master')

@section('title', 'Candidatos | CLDCI')
@section('title-sub', 'Gestión de Candidatos')
@section('pagetitle', 'Candidatos')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .candidato-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        height: 100%;
        background: white;
        text-align: center;
    }
    
    .candidato-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }
    
    .candidato-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        object-fit: cover;
        border: 4px solid #e9ecef;
    }
    
    .candidato-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
    }
    
    .cargo-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .votar-candidato-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }
    
    .votar-candidato-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .propuesta-item {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        text-align: left;
    }
    
    .propuesta-item i {
        color: #667eea;
        margin-right: 0.5rem;
    }
</style>
@endsection

@section('content')
<!-- Estadísticas -->
<div class="row mb-3">
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Total Candidatos"
            value="12"
            icon="ri-user-star-line"
            background="bg-primary-subtle"
            icon-background="bg-primary"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Cargos Disponibles"
            value="5"
            icon="ri-shield-star-line"
            background="bg-success-subtle"
            icon-background="bg-success"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Votos Registrados"
            value="248"
            icon="ri-checkbox-circle-line"
            background="bg-info-subtle"
            icon-background="bg-info"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Participación"
            value="78%"
            icon="ri-percent-line"
            background="bg-warning-subtle"
            icon-background="bg-warning"
        />
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <select class="form-select" id="filtroEleccion">
                            <option value="">Seleccionar Elección</option>
                            <option value="1">Elección Directiva 2024</option>
                            <option value="2">Elección Comité 2024</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtroCargo">
                            <option value="">Todos los Cargos</option>
                            <option value="presidente">Presidente</option>
                            <option value="vicepresidente">Vicepresidente</option>
                            <option value="secretario">Secretario</option>
                            <option value="tesorero">Tesorero</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" placeholder="Buscar candidato...">
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoCandidato">
                            <i class="ri-add-line me-1"></i>
                            Nuevo Candidato
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Candidatos por Cargo -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="ri-shield-star-line me-2"></i>
            Candidatos a Presidente
        </h5>
        
        <div class="row g-4">
            @for($i = 1; $i <= 3; $i++)
            <div class="col-lg-4 col-md-6">
                <div class="candidato-card">
                    @if($i == 1)
                        <img src="https://ui-avatars.com/api/?name=Juan+Pérez&size=120&background=667eea&color=fff" 
                             alt="Candidato" class="candidato-avatar">
                    @else
                        <div class="candidato-avatar-placeholder">
                            {{ chr(64 + $i) }}
                        </div>
                    @endif
                    
                    <h5 class="mb-2">{{ $i == 1 ? 'Juan Pérez García' : ($i == 2 ? 'María González López' : 'Carlos Ramírez Torres') }}</h5>
                    <span class="cargo-badge mb-3">Presidente</span>
                    
                    <p class="text-muted small mb-3">
                        Experiencia de {{ 5 + $i }} años en la organización. Líder comprometido con el desarrollo institucional.
                    </p>
                    
                    <div class="mb-3">
                        <h6 class="text-start mb-2">Propuestas:</h6>
                        <div class="propuesta-item">
                            <i class="ri-checkbox-circle-line"></i>
                            Modernización tecnológica
                        </div>
                        <div class="propuesta-item">
                            <i class="ri-checkbox-circle-line"></i>
                            Transparencia administrativa
                        </div>
                        <div class="propuesta-item">
                            <i class="ri-checkbox-circle-line"></i>
                            Mayor participación social
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="votar-candidato-btn" onclick="votarCandidato({{ $i }})">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            Votar por este Candidato
                        </button>
                        <button class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalles{{ $i }}">
                            <i class="ri-eye-line me-1"></i>
                            Ver Perfil Completo
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<!-- Otros Cargos -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="ri-user-line me-2"></i>
            Candidatos a Vicepresidente
        </h5>
        
        <div class="row g-4">
            @for($i = 1; $i <= 2; $i++)
            <div class="col-lg-4 col-md-6">
                <div class="candidato-card">
                    <div class="candidato-avatar-placeholder">
                        V{{ $i }}
                    </div>
                    
                    <h5 class="mb-2">Candidato Vicepresidente {{ $i }}</h5>
                    <span class="cargo-badge mb-3">Vicepresidente</span>
                    
                    <p class="text-muted small mb-3">
                        Comprometido con el apoyo a la presidencia y la coordinación de proyectos estratégicos.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <button class="votar-candidato-btn" onclick="votarCandidato(10{{ $i }})">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            Votar por este Candidato
                        </button>
                        <button class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetallesV{{ $i }}">
                            <i class="ri-eye-line me-1"></i>
                            Ver Perfil Completo
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<!-- Mensaje de No Hay Candidatos -->
<div class="row" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri-user-search-line display-1 text-muted mb-3"></i>
                <h4>No hay candidatos registrados</h4>
                <p class="text-muted">Aún no se han registrado candidatos para esta elección</p>
                <button class="btn btn-primary mt-3">
                    <i class="ri-add-line me-1"></i>
                    Registrar Primer Candidato
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Candidato -->
<div class="modal fade" id="modalNuevoCandidato" tabindex="-1" aria-labelledby="modalNuevoCandidatoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="modalNuevoCandidatoLabel">
                    <i class="ri-user-add-line me-2"></i>
                    Registrar Nuevo Candidato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoCandidato">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_eleccion_id" class="form-label">
                                    Elección <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="modal_eleccion_id" required>
                                    <option value="">Seleccionar elección...</option>
                                    <option value="1">Elección Directiva 2024</option>
                                    <option value="2">Elección Comité 2024</option>
                                    <option value="3">Elección Especial 2024</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_cargo_id" class="form-label">
                                    Cargo al que Aspira <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="modal_cargo_id" required>
                                    <option value="">Seleccionar cargo...</option>
                                    <option value="1">Presidente</option>
                                    <option value="2">Vicepresidente</option>
                                    <option value="3">Secretario</option>
                                    <option value="4">Tesorero</option>
                                    <option value="5">Vocal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_miembro_id" class="form-label">
                                    Miembro <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="modal_miembro_id" required>
                                    <option value="">Seleccionar miembro...</option>
                                    <option value="1">Juan Pérez García</option>
                                    <option value="2">María González López</option>
                                    <option value="3">Carlos Ramírez Torres</option>
                                    <option value="4">Ana Martínez Ruiz</option>
                                    <option value="5">Luis Fernández Castro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_estado" class="form-label">Estado</label>
                                <select class="form-select" id="modal_estado">
                                    <option value="activo" selected>Activo</option>
                                    <option value="retirado">Retirado</option>
                                    <option value="descalificado">Descalificado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_propuesta" class="form-label">Propuesta/Plan de Trabajo</label>
                        <textarea class="form-control" 
                                  id="modal_propuesta" 
                                  rows="4" 
                                  placeholder="Describe las propuestas y el plan de trabajo del candidato..."></textarea>
                    </div>

                    <!-- Vista Previa del Candidato -->
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="ri-information-line me-2"></i>
                            Vista Previa del Candidato
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                        <i class="ri-user-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0" id="modalPreviewNombre">Selecciona un miembro</h6>
                                <small class="text-muted" id="modalPreviewCargo">Selecciona un cargo</small>
                                <p class="text-muted small mb-0 mt-1" id="modalPreviewPropuesta">Sin propuesta</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCandidato">
                        <i class="ri-save-line me-1"></i>
                        Registrar Candidato
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de Perfil Completo -->
@for($i = 1; $i <= 3; $i++)
<!-- Modal: Perfil Completo Candidato {{ $i }} -->
<div class="modal fade" id="modalDetalles{{ $i }}" tabindex="-1" aria-labelledby="modalDetalles{{ $i }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="modalDetalles{{ $i }}Label">
                    <i class="ri-user-line me-2"></i>
                    Perfil Completo del Candidato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if($i == 1)
                            <img src="https://ui-avatars.com/api/?name=Juan+Pérez&size=200&background=667eea&color=fff" 
                                 alt="Candidato" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                        @else
                            <div class="mx-auto mb-3" style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem; font-weight: 700;">
                                {{ chr(64 + $i) }}
                            </div>
                        @endif
                        
                        <h4 class="mb-2">{{ $i == 1 ? 'Juan Pérez García' : ($i == 2 ? 'María González López' : 'Carlos Ramírez Torres') }}</h4>
                        <span class="badge bg-primary fs-6 mb-3">Candidato a Presidente</span>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="votarCandidato({{ $i }})">
                                <i class="ri-checkbox-circle-line me-1"></i>
                                Votar por este Candidato
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="ri-information-line me-2"></i>
                                    Información Personal
                                </h5>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Edad:</strong><br>
                                        <span class="text-muted">{{ 35 + $i }} años</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Profesión:</strong><br>
                                        <span class="text-muted">{{ $i == 1 ? 'Ingeniero' : ($i == 2 ? 'Abogada' : 'Médico') }}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Experiencia:</strong><br>
                                        <span class="text-muted">{{ 5 + $i }} años en la organización</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="ri-lightbulb-line me-2"></i>
                                    Propuestas Principales
                                </h5>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">Modernización Tecnológica</h6>
                                                <p class="mb-0 text-muted small">Implementar sistemas digitales para mejorar la eficiencia administrativa y la transparencia en los procesos.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">Transparencia Administrativa</h6>
                                                <p class="mb-0 text-muted small">Establecer mecanismos de rendición de cuentas y acceso público a la información institucional.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">Mayor Participación Social</h6>
                                                <p class="mb-0 text-muted small">Fomentar la participación activa de todos los miembros en las decisiones importantes de la organización.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="ri-award-line me-2"></i>
                                    Logros y Experiencia
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title">
                                                    <i class="ri-trophy-line text-warning me-2"></i>
                                                    Logros Destacados
                                                </h6>
                                                <ul class="list-unstyled mb-0 small">
                                                    <li><i class="ri-check-line text-success me-1"></i> Liderazgo en proyectos comunitarios</li>
                                                    <li><i class="ri-check-line text-success me-1"></i> Experiencia en gestión administrativa</li>
                                                    <li><i class="ri-check-line text-success me-1"></i> Compromiso con la transparencia</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title">
                                                    <i class="ri-user-star-line text-info me-2"></i>
                                                    Competencias
                                                </h6>
                                                <ul class="list-unstyled mb-0 small">
                                                    <li><i class="ri-check-line text-success me-1"></i> Liderazgo y comunicación</li>
                                                    <li><i class="ri-check-line text-success me-1"></i> Gestión de equipos</li>
                                                    <li><i class="ri-check-line text-success me-1"></i> Resolución de conflictos</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="votarCandidato({{ $i }})">
                    <i class="ri-checkbox-circle-line me-1"></i>
                    Votar por este Candidato
                </button>
            </div>
        </div>
    </div>
</div>
@endfor

<!-- Modales para Vicepresidentes -->
@for($i = 1; $i <= 2; $i++)
<!-- Modal: Perfil Completo Vicepresidente {{ $i }} -->
<div class="modal fade" id="modalDetallesV{{ $i }}" tabindex="-1" aria-labelledby="modalDetallesV{{ $i }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success-subtle">
                <h5 class="modal-title" id="modalDetallesV{{ $i }}Label">
                    <i class="ri-user-line me-2"></i>
                    Perfil Completo del Candidato a Vicepresidente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mx-auto mb-3" style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem; font-weight: 700;">
                            V{{ $i }}
                        </div>
                        
                        <h4 class="mb-2">Candidato Vicepresidente {{ $i }}</h4>
                        <span class="badge bg-success fs-6 mb-3">Candidato a Vicepresidente</span>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="votarCandidato(10{{ $i }})">
                                <i class="ri-checkbox-circle-line me-1"></i>
                                Votar por este Candidato
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="ri-information-line me-2"></i>
                                    Información Personal
                                </h5>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Edad:</strong><br>
                                        <span class="text-muted">{{ 30 + $i }} años</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Profesión:</strong><br>
                                        <span class="text-muted">{{ $i == 1 ? 'Administrador' : 'Contador' }}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Experiencia:</strong><br>
                                        <span class="text-muted">{{ 3 + $i }} años en la organización</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="ri-lightbulb-line me-2"></i>
                                    Propuestas Principales
                                </h5>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">Apoyo a la Presidencia</h6>
                                                <p class="mb-0 text-muted small">Brindar apoyo estratégico y operativo a las decisiones presidenciales para el mejor funcionamiento de la organización.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">Coordinación de Proyectos</h6>
                                                <p class="mb-0 text-muted small">Coordinar y supervisar la ejecución de proyectos estratégicos y actividades especiales.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="votarCandidato(10{{ $i }})">
                    <i class="ri-checkbox-circle-line me-1"></i>
                    Votar por este Candidato
                </button>
            </div>
        </div>
    </div>
</div>
@endfor
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function votarCandidato(id) {
        // Mostrar modal de confirmación personalizado
        Swal.fire({
            title: '¿Confirmar voto?',
            text: "¿Estás seguro de votar por este candidato?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ri-checkbox-circle-line me-1"></i> Sí, votar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica de votación
                Swal.fire({
                    title: '¡Voto Registrado!',
                    text: 'Tu voto ha sido registrado exitosamente',
                    icon: 'success',
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'Continuar',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
    }
    
    // Filtros
    document.getElementById('filtroEleccion')?.addEventListener('change', function() {
        console.log('Filtrar por elección:', this.value);
    });
    
    document.getElementById('filtroCargo')?.addEventListener('change', function() {
        console.log('Filtrar por cargo:', this.value);
    });

    // Funciones para el modal de nuevo candidato
    function actualizarVistaPreviaCandidato() {
        const miembroSelect = document.getElementById('modal_miembro_id');
        const cargoSelect = document.getElementById('modal_cargo_id');
        const propuestaText = document.getElementById('modal_propuesta');
        
        const nombreMiembro = miembroSelect.options[miembroSelect.selectedIndex]?.text || 'Selecciona un miembro';
        const nombreCargo = cargoSelect.options[cargoSelect.selectedIndex]?.text || 'Selecciona un cargo';
        const propuesta = propuestaText.value || 'Sin propuesta';
        
        document.getElementById('modalPreviewNombre').textContent = nombreMiembro;
        document.getElementById('modalPreviewCargo').textContent = nombreCargo;
        document.getElementById('modalPreviewPropuesta').textContent = propuesta;
    }

    // Event listeners para vista previa en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const modalMiembro = document.getElementById('modal_miembro_id');
        const modalCargo = document.getElementById('modal_cargo_id');
        const modalPropuesta = document.getElementById('modal_propuesta');
        
        if (modalMiembro) modalMiembro.addEventListener('change', actualizarVistaPreviaCandidato);
        if (modalCargo) modalCargo.addEventListener('change', actualizarVistaPreviaCandidato);
        if (modalPropuesta) modalPropuesta.addEventListener('input', actualizarVistaPreviaCandidato);
    });

    // Manejar envío del formulario de nuevo candidato
    document.getElementById('formNuevoCandidato').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnGuardar = document.getElementById('btnGuardarCandidato');
        const btnText = btnGuardar.innerHTML;
        
        // Deshabilitar botón
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
        
        const formData = {
            eleccion_id: document.getElementById('modal_eleccion_id').value,
            cargo_id: document.getElementById('modal_cargo_id').value,
            miembro_id: document.getElementById('modal_miembro_id').value,
            estado: document.getElementById('modal_estado').value,
            propuesta: document.getElementById('modal_propuesta').value
        };
        
        // Simular envío (aquí iría la llamada real al backend)
        setTimeout(() => {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCandidato'));
            modal.hide();
            
            // Limpiar formulario
            document.getElementById('formNuevoCandidato').reset();
            actualizarVistaPreviaCandidato();
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Candidato Registrado!',
                text: 'El candidato ha sido registrado exitosamente',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Aquí podrías recargar la página o actualizar la lista
                window.location.reload();
            });
            
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = btnText;
        }, 1500);
    });
</script>
@endsection

