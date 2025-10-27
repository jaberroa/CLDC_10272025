@props(['miembro', 'cargosActuales'])

<div class="row">
    @forelse($cargosActuales as $cargo)
    <div class="col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                    <div class="h-40px w-40px d-flex justify-content-center align-items-center bg-primary-subtle text-primary fs-5 rounded">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <h6 class="mb-1">{{ $cargo['cargo'] ?? 'Cargo' }}</h6>
                </div>
                <p class="text-muted mb-4">{{ $cargo['descripcion'] ?? 'Descripción del cargo y responsabilidades.' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Desde {{ $cargo['fecha_inicio'] ?? 'N/A' }}</span>
                    </div>
                    <span class="badge bg-success-subtle text-success">Activo</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri-team-line fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">Sin cargos actuales</h5>
                <p class="text-muted">No hay cargos asignados actualmente.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>


