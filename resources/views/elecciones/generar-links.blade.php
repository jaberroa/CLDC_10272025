@extends('partials.layouts.master')

@section('title', 'Generar Links de Votación | CLDCI')
@section('title-sub', 'Links Seguros de Votación')
@section('pagetitle', 'Generar Links: ' . $eleccion->titulo)

@section('css')
<style>
    .link-card {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        background: white;
        transition: all 0.2s;
    }
    
    .link-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .token-url {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        word-break: break-all;
    }
    
    .security-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('elecciones.index') }}" class="btn btn-soft-secondary">
            <i class="ri-arrow-left-line me-1"></i>
            Volver a Elecciones
        </a>
    </div>
</div>

<!-- Info de la Elección -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="ri-shield-check-line me-2"></i>
                    {{ $eleccion->titulo }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Organización:</strong> {{ $eleccion->organizacion->nombre ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Periodo:</strong> {{ $eleccion->fecha_inicio->format('d/m/Y H:i') }} - {{ $eleccion->fecha_fin->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-success">{{ ucfirst($eleccion->estado) }}</span></p>
                        <p class="mb-0"><strong>Tipo:</strong> {{ ucfirst($eleccion->tipo) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Características de Seguridad -->
<div class="alert alert-info">
    <h6 class="alert-heading">
        <i class="ri-information-line me-2"></i>
        🔐 Características de Seguridad de los Links
    </h6>
    <ul class="mb-0">
        <li>✅ <strong>Token JWT firmado con HMAC-SHA256</strong></li>
        <li>✅ <strong>Validez de 30 minutos</strong> (configurable)</li>
        <li>✅ <strong>Un solo uso</strong> - Se invalida automáticamente después de votar</li>
        <li>✅ <strong>No reutilizable</strong> - Prevención de replay attacks</li>
        <li>✅ <strong>Rastreo completo</strong> - IP, timestamp, user agent</li>
        <li>✅ <strong>Rate limiting</strong> - Protección contra abusos</li>
    </ul>
</div>

<!-- Candidatos de la Elección -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="ri-user-star-line me-2"></i>
                    Candidatos de la Elección ({{ $eleccion->candidatos->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($eleccion->candidatos->count() > 0)
                    <div class="row">
                        @foreach($eleccion->candidatos as $candidato)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                    {{ $loop->iteration }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $candidato->miembro->nombre_completo ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-1 small">
                                                <i class="ri-briefcase-4-line me-1"></i>
                                                <strong>Cargo:</strong> {{ $candidato->cargo->nombre ?? 'N/A' }}
                                            </p>
                                            @if($candidato->propuesta)
                                                <p class="text-muted small mb-0">
                                                    <i class="ri-lightbulb-line me-1"></i>
                                                    {{ Str::limit($candidato->propuesta, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="ri-alert-line me-2"></i>
                        Esta elección no tiene candidatos registrados. Agrega candidatos antes de generar links de votación.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Generar Link de Elección -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-link me-2"></i>
                    Generar Link de Votación
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Genera un link único para esta elección que mostrará todos los candidatos 
                    para que los votantes puedan elegir su favorito.
                </p>
                
                <form id="formGenerarLinkEleccion">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Acceso</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo_publico" value="publico" checked>
                                    <label class="form-check-label" for="tipo_publico">
                                        <i class="ri-global-line me-1"></i>
                                        <strong>Público</strong>
                                        <small class="d-block text-muted">Cualquiera puede votar</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo_privado" value="privado">
                                    <label class="form-check-label" for="tipo_privado">
                                        <i class="ri-lock-line me-1"></i>
                                        <strong>Privado</strong>
                                        <small class="d-block text-muted">Requiere login</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" {{ $eleccion->candidatos->count() == 0 ? 'disabled' : '' }}>
                        <i class="ri-link me-1"></i>
                        Generar Link de Votación
                    </button>
                </form>
                
                <!-- Resultado -->
                <div id="resultadoLinkEleccion" class="mt-3" style="display: none;">
                    <div class="alert alert-success">
                        <h6 class="alert-heading">✅ Link de Votación Generado</h6>
                        <div class="mb-2">
                            <strong>URL:</strong>
                            <div class="token-url" id="urlEleccionGenerada"></div>
                        </div>
                        <div class="mb-2">
                            <strong>Tipo:</strong> <span id="tipoGenerado"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Candidatos:</strong> <span id="candidatosCount"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Expira:</strong> <span id="expiraEnEleccion"></span>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-sm btn-primary" onclick="copiarLinkEleccion()">
                                <i class="ri-file-copy-line me-1"></i>
                                Copiar
                            </button>
                            <button class="btn btn-sm btn-info" onclick="enviarPorEmailEleccion()">
                                <i class="ri-mail-line me-1"></i>
                                Enviar por Email
                            </button>
                            <button class="btn btn-sm btn-success" onclick="compartirWhatsAppEleccion()">
                                <i class="ri-whatsapp-line me-1"></i>
                                WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generar Links Masivos -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-group-line me-2"></i>
                    Generar Links Masivos
                </h5>
            </div>
            <div class="card-body">
                <form id="formGenerarMasivo">
                    <!-- Búsqueda y selección de usuarios -->
                    <div class="mb-3">
                        <label class="form-label">Buscar Votantes</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="buscarVotante" 
                                   placeholder="Buscar por nombre o email...">
                            <button class="btn btn-outline-secondary" type="button" id="btnSeleccionarTodos">
                                <i class="ri-checkbox-multiple-line me-1"></i>
                                Todos
                            </button>
                        </div>
                    </div>
                    
                    <!-- Lista de usuarios disponibles -->
                    <div class="mb-3">
                        <label class="form-label">Usuarios Disponibles</label>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="listaDisponibles">
                            @foreach($usuarios as $usuario)
                                <div class="usuario-item d-flex justify-content-between align-items-center p-2 border-bottom" data-user-id="{{ $usuario->id }}" data-name="{{ strtolower($usuario->name) }}" data-email="{{ strtolower($usuario->email) }}">
                                    <div>
                                        <strong>{{ $usuario->name }}</strong>
                                        <br><small class="text-muted">{{ $usuario->email }}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" onclick="agregarVotante({{ $usuario->id }}, '{{ $usuario->name }}', '{{ $usuario->email }}')">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Lista de usuarios seleccionados -->
                    <div class="mb-3">
                        <label class="form-label">
                            Votantes Seleccionados (<span id="contadorSeleccionados">0</span>)
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="limpiarSeleccion()">
                                <i class="ri-delete-bin-line"></i> Limpiar Todo
                            </button>
                        </label>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto; min-height: 100px;" id="listaSeleccionados">
                            <p class="text-muted text-center mt-3">No hay votantes seleccionados</p>
                        </div>
                    </div>

                    <!-- Tipo de Link -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de Link</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="linkPublico" name="link_publico">
                            <label class="form-check-label" for="linkPublico">
                                <i class="ri-global-line me-1"></i>
                                <strong>Link Público</strong>
                                <br>
                                <small class="text-muted">
                                    Si está activo, cualquier persona con el link puede votar (sin verificar identidad)
                                </small>
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-warning" id="alertaPublico" style="display: none;">
                        <i class="ri-alert-line me-2"></i>
                        <strong>Atención:</strong> Los links públicos permiten que cualquiera vote sin autenticación. 
                        Úsalos solo para votaciones abiertas al público general.
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="btnGenerarMasivo" disabled>
                        <i class="ri-links-line me-1"></i>
                        Generar Links Masivos
                    </button>
                </form>
                
                <div id="resultadoMasivo" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">📋 Links Generados</h6>
                        <p class="mb-2">Total: <strong id="totalGenerados">0</strong></p>
                        <button class="btn btn-sm btn-primary" onclick="descargarCSV()">
                            <i class="ri-download-line me-1"></i>
                            Descargar CSV
                        </button>
                        <button class="btn btn-sm btn-success" onclick="copiarTodos()">
                            <i class="ri-file-copy-line me-1"></i>
                            Copiar Todos
                        </button>
                    </div>
                    <div id="listaLinks" class="mt-2" style="max-height: 400px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Links Activos -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-time-line me-2"></i>
                    Links Activos Generados
                </h5>
            </div>
            <div class="card-body">
                <div id="linksActivos">
                    <p class="text-muted text-center">No hay links activos generados aún</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let linkGenerado = '';
let linkEleccionGenerado = '';
let linksMasivos = [];
let votantesSeleccionados = [];

// Generar link de elección
document.getElementById('formGenerarLinkEleccion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando...';
    
    fetch('{{ route("voting.generar-link-eleccion", $eleccion) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tipo: tipo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            linkEleccionGenerado = data.url;
            document.getElementById('urlEleccionGenerada').textContent = data.url;
            document.getElementById('tipoGenerado').textContent = data.tipo;
            document.getElementById('candidatosCount').textContent = data.candidatos_count;
            document.getElementById('expiraEnEleccion').textContent = data.expires_at;
            document.getElementById('resultadoLinkEleccion').style.display = 'block';
            showSuccessToast('Link de votación generado exitosamente');
        } else {
            showErrorToast(data.message || 'Error al generar el link');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error de conexión');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-link me-1"></i>Generar Link de Votación';
    });
});

// Generar link individual
document.getElementById('formGenerarLink').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('user_id').value;
    
    if (!userId) {
        showErrorToast('Por favor selecciona un usuario');
        return;
    }
    
    fetch('{{ route("voting.generar-token", $eleccion) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            linkGenerado = data.data.url;
            document.getElementById('urlGenerada').textContent = data.data.url;
            document.getElementById('expiraEn').textContent = new Date(data.data.expires_at).toLocaleString('es-ES');
            document.getElementById('resultadoLink').style.display = 'block';
            showSuccessToast('Link generado exitosamente');
        } else {
            showErrorToast(data.message || 'Error al generar el link');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al generar el link');
    });
});

// === NUEVA FUNCIONALIDAD: GESTIÓN DE VOTANTES ===

// Búsqueda de votantes
document.getElementById('buscarVotante').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#listaDisponibles .usuario-item');
    
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        const email = item.getAttribute('data-email');
        
        if (name.includes(query) || email.includes(query)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Seleccionar todos
document.getElementById('btnSeleccionarTodos').addEventListener('click', function() {
    const items = document.querySelectorAll('#listaDisponibles .usuario-item');
    items.forEach(item => {
        if (item.style.display !== 'none') {
            const userId = parseInt(item.getAttribute('data-user-id'));
            if (!votantesSeleccionados.find(v => v.id === userId)) {
                const name = item.querySelector('strong').textContent;
                const email = item.querySelector('small').textContent;
                agregarVotante(userId, name, email);
            }
        }
    });
});

// Agregar votante a la selección
function agregarVotante(userId, name, email) {
    // Verificar si ya está seleccionado
    if (votantesSeleccionados.find(v => v.id === userId)) {
        showInfoToast('Este votante ya está seleccionado');
        return;
    }
    
    // Agregar a la lista
    votantesSeleccionados.push({ id: userId, name: name, email: email });
    
    // Actualizar UI
    actualizarListaSeleccionados();
    actualizarContador();
    actualizarBotonGenerar();
}

// Quitar votante de la selección
function quitarVotante(userId) {
    votantesSeleccionados = votantesSeleccionados.filter(v => v.id !== userId);
    actualizarListaSeleccionados();
    actualizarContador();
    actualizarBotonGenerar();
}

// Limpiar toda la selección
function limpiarSeleccion() {
    votantesSeleccionados = [];
    actualizarListaSeleccionados();
    actualizarContador();
    actualizarBotonGenerar();
}

// Actualizar lista visual de seleccionados
function actualizarListaSeleccionados() {
    const container = document.getElementById('listaSeleccionados');
    
    if (votantesSeleccionados.length === 0) {
        container.innerHTML = '<p class="text-muted text-center mt-3">No hay votantes seleccionados</p>';
        return;
    }
    
    let html = '';
    votantesSeleccionados.forEach(votante => {
        html += `
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <div>
                    <strong>${votante.name}</strong>
                    <br><small class="text-muted">${votante.email}</small>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="quitarVotante(${votante.id})">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Actualizar contador
function actualizarContador() {
    document.getElementById('contadorSeleccionados').textContent = votantesSeleccionados.length;
}

// Habilitar/deshabilitar botón de generar
function actualizarBotonGenerar() {
    const btn = document.getElementById('btnGenerarMasivo');
    btn.disabled = votantesSeleccionados.length === 0;
}

// Toggle alerta de link público
document.getElementById('linkPublico').addEventListener('change', function() {
    const alerta = document.getElementById('alertaPublico');
    alerta.style.display = this.checked ? 'block' : 'none';
});

// Generar links masivos (actualizado)
document.getElementById('formGenerarMasivo').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (votantesSeleccionados.length === 0) {
        showErrorToast('Por favor selecciona al menos un votante');
        return;
    }
    
    const linkPublico = document.getElementById('linkPublico').checked;
    const userIds = votantesSeleccionados.map(v => v.id);
    
    const btn = document.getElementById('btnGenerarMasivo');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando...';
    
    fetch('{{ route("voting.generar-tokens-masivos", $eleccion) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            user_ids: userIds,
            link_publico: linkPublico
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            linksMasivos = data.data.links;
            document.getElementById('totalGenerados').textContent = linksMasivos.length;
            
            let html = '';
            linksMasivos.forEach(link => {
                const tipoIcon = linkPublico ? '<i class="ri-global-line text-warning"></i>' : '<i class="ri-lock-line text-success"></i>';
                const tipoBadge = linkPublico ? '<span class="badge bg-warning">Público</span>' : '<span class="badge bg-success">Privado</span>';
                
                html += `
                    <div class="link-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0">${link.name}</h6>
                                    ${tipoBadge}
                                </div>
                                <small class="text-muted">${link.email}</small>
                                <div class="token-url mt-2">${link.url}</div>
                                <small class="text-muted">Expira: ${new Date(link.expires_at).toLocaleString('es-ES')}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="copiarLinkIndividual('${link.url}')">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('listaLinks').innerHTML = html;
            document.getElementById('resultadoMasivo').style.display = 'block';
            showSuccessToast(`${linksMasivos.length} links generados exitosamente`);
            
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-links-line me-1"></i> Generar Links Masivos';
        } else {
            showErrorToast(data.message || 'Error al generar links');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-links-line me-1"></i> Generar Links Masivos';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al generar links masivos');
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-links-line me-1"></i> Generar Links Masivos';
    });
});

function copiarLink() {
    navigator.clipboard.writeText(linkGenerado).then(() => {
        showSuccessToast('Link copiado al portapapeles');
    });
}

function copiarLinkEleccion() {
    navigator.clipboard.writeText(linkEleccionGenerado).then(() => {
        showSuccessToast('Link de votación copiado al portapapeles');
    });
}

function copiarLinkIndividual(url) {
    navigator.clipboard.writeText(url).then(() => {
        showSuccessToast('Link copiado');
    });
}

function enviarPorEmailEleccion() {
    const subject = encodeURIComponent('Link de Votación - {{ $eleccion->titulo }}');
    const body = encodeURIComponent(`Hola,\n\nTe comparto el link para votar en la elección: {{ $eleccion->titulo }}\n\nLink: ${linkEleccionGenerado}\n\nEste link expira en 2 horas.\n\n¡Gracias por participar!`);
    window.open(`mailto:?subject=${subject}&body=${body}`);
}

function compartirWhatsAppEleccion() {
    const text = encodeURIComponent(`¡Hola! Te comparto el link para votar en la elección: {{ $eleccion->titulo }}\n\n${linkEleccionGenerado}\n\n¡Tu voto es importante!`);
    window.open(`https://wa.me/?text=${text}`);
}

function copiarTodos() {
    const texto = linksMasivos.map(link => 
        `${link.name} (${link.email}): ${link.url}`
    ).join('\n\n');
    
    navigator.clipboard.writeText(texto).then(() => {
        showSuccessToast('Todos los links copiados');
    });
}

function descargarCSV() {
    let csv = 'Nombre,Email,URL,Expira\n';
    linksMasivos.forEach(link => {
        csv += `"${link.name}","${link.email}","${link.url}","${link.expires_at}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'links-votacion-{{ $eleccion->id }}.csv';
    a.click();
}

function enviarPorEmail() {
    const subject = encodeURIComponent('Link de Votación - {{ $eleccion->titulo }}');
    const body = encodeURIComponent(`Hola,\n\nTe comparto el link para votar en: {{ $eleccion->titulo }}\n\n${linkGenerado}\n\nEste link es de un solo uso y expira en 30 minutos.\n\n¡Gracias!`);
    window.open(`mailto:?subject=${subject}&body=${body}`);
}

function compartirWhatsApp() {
    const texto = encodeURIComponent(`🗳️ Link de Votación\n\n{{ $eleccion->titulo }}\n\n${linkGenerado}\n\n⚠️ Este link es de un solo uso y expira en 30 minutos.`);
    window.open(`https://wa.me/?text=${texto}`, '_blank');
}
</script>
@endsection

