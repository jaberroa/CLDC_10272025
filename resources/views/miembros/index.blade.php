@extends('partials.layouts.master')

@section('title', 'Miembros | CLDCI')
@section('title-sub', 'Gestión de Miembros')
@section('pagetitle', 'Miembros')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
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
                                <i class="ri-user-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Total Miembros</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_miembros']) }}</h4>
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
                                <i class="ri-user-check-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Miembros Activos</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['miembros_activos']) }}</h4>
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
                                <i class="ri-building-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Organizaciones</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($organizaciones->count()) }}</h4>
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
                                <i class="ri-calendar-line" id="hexagon"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Nuevos Este Mes</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['por_tipo']->sum('cantidad')) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Filtros de Búsqueda</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('miembros.index') }}" id="filtros-form">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="buscar" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="buscar" name="buscar" 
                                   value="{{ request('buscar') }}" placeholder="Nombre, cédula o carnet">
                        </div>
                        <div class="col-md-2">
                            <label for="tipo_membresia" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo_membresia" name="tipo_membresia">
                                <option value="">Todos</option>
                                <option value="fundador" {{ request('tipo_membresia') == 'fundador' ? 'selected' : '' }}>Fundador</option>
                                <option value="activo" {{ request('tipo_membresia') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="pasivo" {{ request('tipo_membresia') == 'pasivo' ? 'selected' : '' }}>Pasivo</option>
                                <option value="honorifico" {{ request('tipo_membresia') == 'honorifico' ? 'selected' : '' }}>Honorífico</option>
                                <option value="estudiante" {{ request('tipo_membresia') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="diaspora" {{ request('tipo_membresia') == 'diaspora' ? 'selected' : '' }}>Diáspora</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="estado_membresia" class="form-label">Estado</label>
                            <select class="form-select" id="estado_membresia" name="estado_membresia">
                                <option value="">Todos</option>
                                <option value="activa" {{ request('estado_membresia') == 'activa' ? 'selected' : '' }}>Activa</option>
                                <option value="suspendida" {{ request('estado_membresia') == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                <option value="inactiva" {{ request('estado_membresia') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                <option value="honoraria" {{ request('estado_membresia') == 'honoraria' ? 'selected' : '' }}>Honoraria</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="organizacion_id" class="form-label">Organización</label>
                            <select class="form-select" id="organizacion_id" name="organizacion_id">
                                <option value="">Todas</option>
                                @foreach($organizaciones as $org)
                                <option value="{{ $org->id }}" {{ request('organizacion_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-search-line me-1"></i> Buscar
                                </button>
                                <a href="{{ route('miembros.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Miembros -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">Lista de Miembros</h4>
                    <div class="ms-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="exportarMiembros()">
                                <i class="ri-download-line me-1"></i> Exportar
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="imprimirLista()">
                                <i class="ri-printer-line me-1"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Miembro</th>
                                <th>Número Carnet</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Organización</th>
                                <th>Fecha Ingreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($miembros as $miembro)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($miembro->foto_url)
                                            <img src="{{ $miembro->foto_url }}" alt="" class="avatar-xs rounded-circle">
                                            @else
                                            <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                <i class="ri-user-line fs-12"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0">{{ $miembro->nombre_completo }}</h6>
                                            <p class="text-muted mb-0 fs-12">{{ $miembro->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $miembro->numero_carnet }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $miembro->tipo_membresia === 'fundador' ? 'primary' : ($miembro->tipo_membresia === 'activo' ? 'success' : 'info') }}">
                                        {{ ucfirst($miembro->tipo_membresia) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $miembro->estado_membresia === 'activa' ? 'success' : ($miembro->estado_membresia === 'suspendida' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($miembro->estado_membresia) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ Str::limit($miembro->organizacion->nombre, 20) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $miembro->fecha_ingreso->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('miembros.show', $miembro->id) }}">
                                                <i class="ri-eye-line me-2"></i> Ver Detalles
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('miembros.carnet', $miembro->id) }}" target="_blank">
                                                <i class="ri-qr-code-line me-2"></i> Ver Carnet
                                            </a></li>
                                            <li><a class="dropdown-item" href="mailto:{{ $miembro->email }}">
                                                <i class="ri-mail-line me-2"></i> Enviar Email
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#">
                                                <i class="ri-user-settings-line me-2"></i> Editar
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <div class="py-4">
                                        <i class="ri-user-line fs-48 text-muted mb-3 d-block"></i>
                                        <h5>No se encontraron miembros</h5>
                                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($miembros->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $miembros->firstItem() }} a {{ $miembros->lastItem() }} de {{ $miembros->total() }} resultados
                    </div>
                    <div>
                        {{ $miembros->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('#organizacion_id').select2({
        placeholder: 'Seleccionar organización',
        allowClear: true
    });

    // Auto-submit form on filter change
    $('#tipo_membresia, #estado_membresia').on('change', function() {
        $('#filtros-form').submit();
    });
});

function exportarMiembros() {
    const form = document.getElementById('filtros-form');
    const action = form.action;
    form.action = '{{ route("miembros.exportar") }}';
    form.submit();
    form.action = action;
}

function imprimirLista() {
    window.print();
}
</script>
@endsection

