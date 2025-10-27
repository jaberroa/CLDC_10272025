@extends('partials.layouts.master')

@section('title', 'Mis Aprobaciones Pendientes | CLDCI')
@section('pagetitle', 'Mis Aprobaciones Pendientes')

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.dashboard') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver al Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    Documentos Pendientes de Aprobación
                </h5>
            </div>
            <div class="card-body">
                @forelse($aprobaciones as $aprobacion)
                <div class="border rounded p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $aprobacion->documento->titulo }}</h6>
                            <p class="text-muted mb-0 small">
                                <i class="ri-folder-line me-1"></i>
                                {{ $aprobacion->documento->seccion->nombre }} / {{ $aprobacion->documento->carpeta->nombre }}
                            </p>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="badge bg-warning">Pendiente</span>
                            <p class="text-muted small mb-0 mt-1">
                                Solicitado {{ $aprobacion->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-success" onclick="aprobarDocumento({{ $aprobacion->id }})">
                                <i class="ri-check-line me-1"></i> Aprobar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rechazarDocumento({{ $aprobacion->id }})">
                                <i class="ri-close-line me-1"></i> Rechazar
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="ri-checkbox-circle-line display-1 text-muted"></i>
                    <h5 class="mt-3">No tienes aprobaciones pendientes</h5>
                    <p class="text-muted">Todas las solicitudes están al día</p>
                </div>
                @endforelse
                
                @if($aprobaciones->hasPages())
                <div class="mt-3">
                    {{ $aprobaciones->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="aprobarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Aprobar Documento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Comentarios (opcional)</label>
                    <textarea class="form-control" name="comentarios" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="rechazarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Rechazar Documento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Razón del rechazo <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="razon_rechazo" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function aprobarDocumento(aprobacionId) {
        document.getElementById('formAprobar').action = `/gestion-documental/aprobaciones/${aprobacionId}/aprobar`;
        new bootstrap.Modal(document.getElementById('aprobarModal')).show();
    }

    function rechazarDocumento(aprobacionId) {
        document.getElementById('formRechazar').action = `/gestion-documental/aprobaciones/${aprobacionId}/rechazar`;
        new bootstrap.Modal(document.getElementById('rechazarModal')).show();
    }
</script>
@endsection

