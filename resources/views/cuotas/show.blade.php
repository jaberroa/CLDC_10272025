@extends('partials.layouts.master')

@section('title', 'Detalles de Cuota | CLDCI')
@section('title-sub', 'Información de Cuota')
@section('pagetitle', 'Detalles de Cuota')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-8">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('cuotas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-money-dollar-circle-line"></i>
                            Detalles de Cuota
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Información completa de la cuota de {{ $cuota->miembro->nombre_completo }}
                        </p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('miembros.profile', $cuota->miembro) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-user-line me-1"></i> Ver Miembro
                        </a>
                        <a href="{{ route('cuotas.edit', $cuota) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-edit-line me-1"></i> Editar
                        </a>
                        @if($cuota->estado === 'pendiente')
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="marcarComoPagada({{ $cuota->id }})">
                                <i class="ri-check-line me-1"></i> Marcar Pagada
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="deleteCuota({{ $cuota->id }}, '{{ $cuota->miembro->nombre_completo }}')">
                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Miembro</label>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="ri-user-line text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $cuota->miembro->nombre_completo }}</h6>
                                <small class="text-muted">{{ $cuota->miembro->numero_carnet }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo de Cuota</label>
                        <div>
                            <span class="badge bg-info-subtle text-info fs-6">
                                {{ ucfirst($cuota->tipo_cuota) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Monto</label>
                        <div class="h4 text-primary mb-0">RD$ {{ number_format($cuota->monto, 2) }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado</label>
                        <div>
                            <span class="badge bg-{{ $cuota->estado_color }}-subtle text-{{ $cuota->estado_color }} fs-6">
                                {{ ucfirst($cuota->estado) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vencimiento</label>
                        <div class="h6 mb-0">{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</div>
                        @if($cuota->estaVencida())
                            <small class="text-danger">
                                <i class="ri-time-line me-1"></i> Vencida hace {{ $cuota->fecha_vencimiento->diffForHumans() }}
                            </small>
                        @elseif($cuota->fecha_vencimiento->isFuture())
                            <small class="text-muted">
                                <i class="ri-time-line me-1"></i> Vence en {{ $cuota->fecha_vencimiento->diffForHumans() }}
                            </small>
                        @endif
                    </div>

                    @if($cuota->fecha_pago)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de Pago</label>
                        <div class="h6 mb-0">{{ $cuota->fecha_pago->format('d/m/Y') }}</div>
                    </div>
                    @endif

                    @if($cuota->recurrente)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Recurrente</label>
                        <div>
                            <span class="badge bg-success-subtle text-success fs-6">
                                <i class="ri-refresh-line me-1"></i> Sí - {{ ucfirst($cuota->frecuencia_recurrencia) }}
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($cuota->observaciones)
                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <div class="p-3 bg-light rounded">
                            {{ $cuota->observaciones }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Información Adicional integrada -->
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-semibold text-muted mb-3">
                            <i class="ri-information-line me-2"></i>Información Adicional
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de Creación</label>
                        <div class="text-muted">{{ $cuota->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($cuota->updated_at != $cuota->created_at)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Última Actualización</label>
                        <div class="text-muted">{{ $cuota->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>

                <!-- Acciones integradas -->
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-semibold text-muted mb-3">
                            <i class="ri-settings-3-line me-2"></i>Acciones
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('miembros.profile', $cuota->miembro) }}" class="btn btn-outline-primary w-100">
                            <i class="ri-user-line me-1"></i> Ver Miembro
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('cuotas.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ri-arrow-left-line me-1"></i> Volver a Lista
                        </a>
                    </div>
                    @if($cuota->estado === 'pendiente')
                    <div class="col-12">
                        <button type="button" class="btn btn-success w-100" onclick="marcarComoPagada({{ $cuota->id }})">
                            <i class="ri-check-line me-1"></i> Marcar como Pagada
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para marcar como pagada -->
<div class="modal fade" id="marcarPagadaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marcar Cuota como Pagada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="marcarPagadaForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comprobante_url" class="form-label">URL del Comprobante (opcional)</label>
                        <input type="url" class="form-control" id="comprobante_url" name="comprobante_url" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Pagada</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function marcarComoPagada(cuotaId) {
    const form = document.getElementById('marcarPagadaForm');
    form.action = `/cuotas/${cuotaId}/marcar-pagada`;
    
    const modal = new bootstrap.Modal(document.getElementById('marcarPagadaModal'));
    modal.show();
}

function deleteCuota(cuotaId, cuotaName) {
    if (confirm(`¿Está seguro de eliminar la cuota de "${cuotaName}"?`)) {
        fetch(`/cuotas/${cuotaId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast(`Cuota de "${cuotaName}" eliminada exitosamente`);
                setTimeout(() => {
                    window.location.href = '{{ route("cuotas.index") }}';
                }, 2000);
            } else {
                throw new Error('Error al eliminar la cuota');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al eliminar la cuota');
        });
    }
}

// Funciones de toast
function showSuccessToast(message) {
    // Implementar toast de éxito
    console.log('Success:', message);
}

function showErrorToast(message) {
    // Implementar toast de error
    console.log('Error:', message);
}
</script>
@endsection
