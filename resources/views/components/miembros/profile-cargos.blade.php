@props(['miembro', 'cargosActuales'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Cargos Actuales</h5>
    </div>
        <div class="card-body">
            @if($cargosActuales && count($cargosActuales) > 0)
                <div class="row g-3">
                    @foreach($cargosActuales as $cargo)
                    <div class="col-md-6 col-lg-4">
                        <div class="cargo-item card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-lg bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-user-star-line fs-20"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $cargo['titulo'] ?? 'Cargo' }}</h6>
                                    <p class="text-muted mb-2 fs-13">{{ $cargo['descripcion'] ?? 'Descripción del cargo' }}</p>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary">{{ $cargo['periodo'] ?? '2024-2025' }}</span>
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $cargo['progreso'] ?? 75 }}%" aria-valuenow="{{ $cargo['progreso'] ?? 75 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="avatar-xl bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ri-user-star-line fs-24 text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-2">Sin Cargos Asignados</h5>
                    <p class="text-muted mb-0">Este miembro no tiene cargos asignados actualmente.</p>
                </div>
            @endif
        </div>
    </div>
</div>
