@extends('partials.layouts.master')

@section('title', 'Directiva | CLDCI')
@section('title-sub', 'Estructura Organizacional')
@section('pagetitle', 'Directiva')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/orgchart/orgchart.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
@endsection

@section('content')
<div class="row">
    <!-- Estadísticas -->
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-primary-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-primary d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-building-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Total Órganos</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_organos']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-success-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-success d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-team-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Directivos Activos</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_directivos']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-warning-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-warning d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-crown-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Presidentes</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['presidentes']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-info-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-info d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-hierarchy-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Niveles</span>
                                <h4 class="mb-0 fw-semibold">{{ $estadisticas['por_nivel']->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">Estructura Organizacional</h4>
                    <div class="ms-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="exportarEstructura()">
                                <i class="ri-download-line me-1"></i> Exportar
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="imprimirEstructura()">
                                <i class="ri-printer-line me-1"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Vista de Organigrama -->
                <div id="organigrama-container" style="min-height: 500px;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando estructura organizacional...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estructura por Niveles -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Estructura por Niveles Jerárquicos</h4>
            </div>
            <div class="card-body">
                @foreach($estructura as $nivel => $organos)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <span class="badge bg-primary me-2">Nivel {{ $nivel }}</span>
                        {{ $organos->count() }} órgano(s)
                    </h5>
                    
                    <div class="row">
                        @foreach($organos as $organo)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-{{ $organo->tipo === 'direccion' ? 'primary' : ($organo->tipo === 'consultivo' ? 'success' : 'info') }}-subtle text-{{ $organo->tipo === 'direccion' ? 'primary' : ($organo->tipo === 'consultivo' ? 'success' : 'info') }} d-flex align-items-center justify-content-center">
                                                <i class="ri-{{ $organo->tipo === 'direccion' ? 'building' : ($organo->tipo === 'consultivo' ? 'user-settings' : 'team') }}-line fs-16"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0">{{ $organo->nombre }}</h6>
                                            <p class="text-muted mb-0 fs-12">{{ ucfirst($organo->tipo) }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($organo->miembrosDirectivos->count() > 0)
                                    <div class="mb-2">
                                        <h6 class="fs-14 mb-2">Miembros Directivos:</h6>
                                        @foreach($organo->miembrosDirectivos as $miembroDirectivo)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="flex-shrink-0">
                                                @if($miembroDirectivo->miembro->foto_url)
                                                <img src="{{ $miembroDirectivo->miembro->foto_url }}" alt="" class="avatar-xs rounded-circle">
                                                @else
                                                <div class="avatar-xs rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center">
                                                    <i class="ri-user-line fs-10"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 fs-12">{{ $miembroDirectivo->miembro->nombre_completo }}</h6>
                                                <p class="text-muted mb-0 fs-11">
                                                    {{ $miembroDirectivo->cargo->nombre }}
                                                    @if($miembroDirectivo->es_presidente)
                                                    <span class="badge bg-warning ms-1 fs-10">Presidente</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center text-muted py-2">
                                        <i class="ri-user-line fs-16 mb-1 d-block"></i>
                                        <p class="mb-0 fs-12">Sin miembros asignados</p>
                                    </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge bg-{{ $organo->activo ? 'success' : 'danger' }} fs-10">
                                            {{ $organo->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <button class="btn btn-soft-primary btn-sm" onclick="verDetallesOrgano('{{ $organo->id }}')">
                                            <i class="ri-eye-line me-1"></i> Ver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Timeline de Cambios -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Timeline de Cambios</h4>
            </div>
            <div class="card-body">
                <div id="timeline-container">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <span class="ms-2">Cargando timeline...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del órgano -->
<div class="modal fade" id="modalDetallesOrgano" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Órgano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetallesContent">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/orgchart/orgchart.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar organigrama
    cargarOrganigrama();
    
    // Cargar timeline
    cargarTimeline();
});

function cargarOrganigrama() {
    fetch('{{ route("directiva.organigrama") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('organigrama-container');
            container.innerHTML = '<div id="organigrama-chart"></div>';
            
            // Crear organigrama con OrgChart
            const chart = new OrgChart(document.getElementById('organigrama-chart'), {
                data: data,
                nodeContent: 'title',
                direction: 'top',
                nodeId: 'id',
                parentNodeSymbol: 'fa-users',
                createNode: function(node, data) {
                    const content = `
                        <div class="org-node">
                            <div class="org-title">${data.nombre}</div>
                            <div class="org-type">${data.tipo}</div>
                            ${data.miembros.map(m => `
                                <div class="org-member">
                                    <strong>${m.nombre}</strong>
                                    <br><small>${m.cargo}</small>
                                </div>
                            `).join('')}
                        </div>
                    `;
                    node.innerHTML = content;
                }
            });
        })
        .catch(error => {
            console.error('Error cargando organigrama:', error);
            document.getElementById('organigrama-container').innerHTML = 
                '<div class="text-center text-muted py-5"><i class="ri-error-warning-line fs-48 mb-3 d-block"></i><p>Error cargando organigrama</p></div>';
        });
}

function cargarTimeline() {
    fetch('{{ route("directiva.timeline") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('timeline-container');
            
            if (data.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3"><i class="ri-time-line fs-24 mb-2 d-block"></i><p>No hay cambios recientes</p></div>';
                return;
            }
            
            let timelineHtml = '<div class="timeline">';
            
            data.forEach((item, index) => {
                timelineHtml += `
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${item.miembro}</h6>
                                    <p class="text-muted mb-1 fs-12">
                                        <strong>${item.cargo}</strong> en ${item.organo}
                                        ${item.es_presidente ? '<span class="badge bg-warning ms-1 fs-10">Presidente</span>' : ''}
                                    </p>
                                    <small class="text-muted">${item.fecha}</small>
                                </div>
                                <span class="badge bg-${item.accion === 'Asignado' ? 'success' : 'danger'} fs-10">
                                    ${item.accion}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            timelineHtml += '</div>';
            container.innerHTML = timelineHtml;
        })
        .catch(error => {
            console.error('Error cargando timeline:', error);
            document.getElementById('timeline-container').innerHTML = 
                '<div class="text-center text-muted py-3"><i class="ri-error-warning-line fs-24 mb-2 d-block"></i><p>Error cargando timeline</p></div>';
        });
}

function verDetallesOrgano(organoId) {
    fetch(`/directiva/organo/${organoId}`)
        .then(response => response.json())
        .then(data => {
            const modal = new bootstrap.Modal(document.getElementById('modalDetallesOrgano'));
            document.getElementById('modalDetallesContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Órgano</h6>
                        <p><strong>Nombre:</strong> ${data.organo.nombre}</p>
                        <p><strong>Tipo:</strong> ${data.organo.tipo}</p>
                        <p><strong>Nivel:</strong> ${data.organo.nivel_jerarquico}</p>
                        <p><strong>Descripción:</strong> ${data.organo.descripcion || 'Sin descripción'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Miembros Directivos</h6>
                        ${data.miembros.map(miembro => `
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    ${miembro.miembro.foto_url ? 
                                        `<img src="${miembro.miembro.foto_url}" alt="" class="avatar-sm rounded-circle">` :
                                        `<div class="avatar-sm rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center">
                                            <i class="ri-user-line fs-12"></i>
                                        </div>`
                                    }
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0 fs-14">${miembro.miembro.nombre_completo}</h6>
                                    <p class="text-muted mb-0 fs-12">
                                        ${miembro.cargo.nombre}
                                        ${miembro.es_presidente ? '<span class="badge bg-warning ms-1 fs-10">Presidente</span>' : ''}
                                    </p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            modal.show();
        })
        .catch(error => {
            console.error('Error cargando detalles:', error);
        });
}

function exportarEstructura() {
    window.location.href = '{{ route("directiva.exportar") }}';
}

function imprimirEstructura() {
    window.print();
}
</script>

<style>
.org-node {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    min-width: 150px;
}

.org-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.org-type {
    font-size: 12px;
    color: #666;
    margin-bottom: 10px;
}

.org-member {
    font-size: 11px;
    margin: 2px 0;
    padding: 2px;
    background: #f8f9fa;
    border-radius: 4px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    background: #007bff;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #007bff;
}
</style>
@endsection
