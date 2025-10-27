@extends(isset($payload['public']) && $payload['public'] ? 'partials.layouts.public' : 'partials.layouts.master')

@section('title', 'Votar - ' . $eleccion->titulo . ' | CLDCI')
@section('title-sub', 'Votación Segura')
@section('pagetitle', $eleccion->titulo)

@section('css')
<style>
    .candidato-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s;
        cursor: pointer;
        background: white;
    }
    
    .candidato-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        transform: translateY(-2px);
    }
    
    .candidato-card.selected {
        border-color: #0d6efd;
        background: linear-gradient(135deg, #e7f3ff 0%, #ffffff 100%);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .candidato-card.selected::before {
        content: "✓";
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background: #0d6efd;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .candidato-card {
        position: relative;
    }
    
    .security-badge {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .countdown {
        font-size: 1.2rem;
        font-weight: 600;
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<!-- Info de Seguridad -->
@if(isset($payload['public']) && $payload['public'])
<div class="alert alert-warning mb-4">
    <div class="d-flex align-items-center">
        <i class="ri-global-line fs-3 me-3"></i>
        <div>
            <h6 class="mb-1">🌐 Votación Pública</h6>
            <small>Esta es una votación abierta. No se requiere autenticación para votar.</small>
        </div>
    </div>
</div>
@else
<div class="alert alert-success mb-4">
    <div class="d-flex align-items-center">
        <i class="ri-shield-check-line fs-3 me-3"></i>
        <div>
            <h6 class="mb-1">🔐 Votación Segura Activada</h6>
            <small>Tu voto es anónimo y está protegido con encriptación de nivel bancario</small>
        </div>
    </div>
</div>
@endif

<!-- Info de la Elección -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">{{ $eleccion->titulo }}</h3>
                @if($eleccion->descripcion)
                    <p class="text-muted mb-3">{{ $eleccion->descripcion }}</p>
                @endif
                
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="badge bg-soft-info text-info">
                        <i class="ri-building-line me-1"></i>
                        {{ $eleccion->organizacion->nombre ?? 'N/A' }}
                    </span>
                    <span class="badge bg-soft-primary text-primary">
                        <i class="ri-calendar-line me-1"></i>
                        {{ $eleccion->fecha_inicio->format('d/m/Y') }} - {{ $eleccion->fecha_fin->format('d/m/Y') }}
                    </span>
                </div>
                
                <div class="alert alert-warning">
                    <i class="ri-time-line me-2"></i>
                    <strong>Tiempo restante del link:</strong> 
                    <span id="countdown" class="countdown"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Candidatos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-user-star-line me-2"></i>
                    Selecciona tu Candidato
                </h5>
            </div>
            <div class="card-body">
                @if($eleccion->candidatos->isEmpty())
                    <div class="text-center py-5">
                        <i class="ri-user-line fs-1 text-muted d-block mb-3"></i>
                        <h5>No hay candidatos registrados</h5>
                        <p class="text-muted">Esta elección aún no tiene candidatos disponibles.</p>
                    </div>
                @else
                    <form id="formVotar">
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        @foreach($eleccion->candidatos->sortBy('orden') as $candidato)
                            <div class="candidato-card mb-3" onclick="seleccionarCandidato({{ $candidato->id }})">
                                <div class="card shadow-sm hover-shadow transition">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="avatar-xl">
                                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary" style="width: 70px; height: 70px;">
                                                        <i class="ri-user-3-line" style="font-size: 32px;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h4 class="mb-2">{{ $candidato->miembro->nombre_completo ?? 'N/A' }}</h4>
                                                <p class="text-muted mb-2">
                                                    <i class="ri-briefcase-4-line me-1"></i>
                                                    <strong>Cargo:</strong> <span class="badge bg-primary-subtle text-primary">{{ $candidato->cargo->nombre ?? 'N/A' }}</span>
                                                </p>
                                                @if($candidato->propuesta)
                                                    <p class="text-muted small mb-0">
                                                        <i class="ri-lightbulb-line me-1"></i>
                                                        {{ $candidato->propuesta }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input type="radio" 
                                                           name="candidato_id" 
                                                           value="{{ $candidato->id }}" 
                                                           id="candidato_{{ $candidato->id }}"
                                                           class="form-check-input" 
                                                           style="width: 24px; height: 24px;"
                                                           required>
                                                    <label class="form-check-label visually-hidden" for="candidato_{{ $candidato->id }}">
                                                        Votar por {{ $candidato->nombre }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5" id="btnVotar" disabled>
                                <i class="ri-checkbox-circle-line me-2"></i>
                                Confirmar mi Voto
                            </button>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Una vez enviado, tu voto no podrá ser modificado
                            </small>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    Confirmar Voto
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="ri-question-line fs-1 text-warning mb-3 d-block"></i>
                <h5 class="mb-3">¿Estás seguro de tu elección?</h5>
                <p class="text-muted mb-4">
                    Estás a punto de votar por:<br>
                    <strong id="candidatoSeleccionado" class="fs-5"></strong>
                </p>
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Importante:</strong> Esta acción no se puede deshacer
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarVoto()">
                    <i class="ri-check-line me-1"></i>
                    Sí, Confirmar mi Voto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Éxito -->
<div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="ri-checkbox-circle-fill me-2"></i>
                    ¡Voto Registrado!
                </h5>
            </div>
            <div class="modal-body text-center py-5">
                <i class="ri-check-double-line fs-1 text-success mb-3 d-block"></i>
                <h4 class="mb-3">¡Gracias por Votar!</h4>
                <p class="text-muted mb-4">Tu voto ha sido registrado exitosamente y de forma anónima.</p>
                
                <div class="alert alert-info">
                    <strong>Hash de verificación:</strong><br>
                    <code id="voteHash" style="word-break: break-all;"></code>
                </div>
                
                <small class="text-muted">
                    Guarda este hash para verificar que tu voto fue contabilizado
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="cerrarVentana()">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let candidatoIdSeleccionado = null;
let candidatoNombreSeleccionado = '';

// Countdown timer
const expTimestamp = {{ $payload['exp'] }};
const countdownEl = document.getElementById('countdown');

function actualizarCountdown() {
    const now = Math.floor(Date.now() / 1000);
    const remaining = expTimestamp - now;
    
    if (remaining <= 0) {
        countdownEl.textContent = '¡EXPIRADO!';
        countdownEl.classList.add('text-danger');
        document.getElementById('btnVotar').disabled = true;
        showErrorToast('El link de votación ha expirado');
        return;
    }
    
    const minutes = Math.floor(remaining / 60);
    const seconds = remaining % 60;
    countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (remaining < 300) { // Menos de 5 minutos
        countdownEl.classList.add('text-danger');
    }
}

actualizarCountdown();
setInterval(actualizarCountdown, 1000);

// Seleccionar candidato
function seleccionarCandidato(candidatoId) {
    // Remover selección anterior
    document.querySelectorAll('.candidato-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Seleccionar nuevo
    event.currentTarget.classList.add('selected');
    
    // Marcar radio button
    const radio = event.currentTarget.querySelector('input[type="radio"]');
    radio.checked = true;
    
    // Habilitar botón
    document.getElementById('btnVotar').disabled = false;
    
    // Guardar selección
    candidatoIdSeleccionado = candidatoId;
    candidatoNombreSeleccionado = event.currentTarget.querySelector('h5').textContent;
}

// Enviar formulario
document.getElementById('formVotar').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!candidatoIdSeleccionado) {
        showErrorToast('Por favor selecciona un candidato');
        return;
    }
    
    // Mostrar modal de confirmación
    document.getElementById('candidatoSeleccionado').textContent = candidatoNombreSeleccionado;
    new bootstrap.Modal(document.getElementById('modalConfirmar')).show();
});

function enviarVoto() {
    const btnConfirmar = event.target;
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    
    fetch('{{ route("voting.submit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            token: '{{ $token }}',
            candidato_id: candidatoIdSeleccionado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal de confirmación
            bootstrap.Modal.getInstance(document.getElementById('modalConfirmar')).hide();
            
            // Mostrar hash de verificación
            document.getElementById('voteHash').textContent = data.vote_hash || 'N/A';
            
            // Mostrar modal de éxito
            new bootstrap.Modal(document.getElementById('modalExito')).show();
            
            showSuccessToast('¡Voto registrado exitosamente!');
        } else {
            showErrorToast(data.message || 'Error al registrar el voto');
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = '<i class="ri-check-line me-1"></i> Sí, Confirmar mi Voto';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al enviar el voto');
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = '<i class="ri-check-line me-1"></i> Sí, Confirmar mi Voto';
    });
}

function cerrarVentana() {
    window.close();
    // Si no se puede cerrar, redirigir
    setTimeout(() => {
        window.location.href = '{{ route("elecciones.index") }}';
    }, 500);
}
</script>
@endsection

