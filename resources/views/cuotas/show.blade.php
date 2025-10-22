@extends('partials.layouts.master')

@section('title', 'Detalles de Cuota | CLDCI')
@section('title-sub', 'Información de Cuota')
@section('pagetitle', 'Detalles de Cuota')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-profile.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('cuotas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-eye-line"></i>
                            Detalles de Cuota
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Información completa de la cuota de {{ $cuota->miembro->nombre_completo }}
                        </p>
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
                                    {{ $cuota->tipo_cuota_label }}
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

                        @if($cuota->metodo_pago)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Método de Pago</label>
                            <div class="h6 mb-0">{{ ucfirst($cuota->metodo_pago) }}</div>
                        </div>
                        @endif

                        @if($cuota->comprobante_url)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Comprobante</label>
                            <div>
                                <a href="{{ $cuota->comprobante_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="ri-external-link-line me-1"></i> Ver Comprobante
                                </a>
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
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    @if($cuota->estado === 'pendiente')
                    <button type="button" class="btn btn-success w-100 mb-2" onclick="marcarComoPagada({{ $cuota->id }})">
                        <i class="ri-check-line me-1"></i> Marcar como Pagada
                    </button>
                    @endif

                    <a href="{{ route('miembros.profile', $cuota->miembro) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="ri-user-line me-1"></i> Ver Miembro
                    </a>

                    <a href="{{ route('cuotas.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ri-arrow-left-line me-1"></i> Volver a Lista
                    </a>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información Adicional</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Creado por</label>
                            <div class="text-muted">
                                @if($cuota->creadoPor)
                                    {{ $cuota->creadoPor->name }}
                                @else
                                    Sistema
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Fecha de Creación</label>
                            <div class="text-muted">{{ $cuota->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        @if($cuota->updated_at != $cuota->created_at)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Última Actualización</label>
                            <div class="text-muted">{{ $cuota->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
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
</script>
@endsection
