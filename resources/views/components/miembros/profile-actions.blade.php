@props(['miembro'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Acciones Rápidas</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn btn-outline-primary w-100">
                    <i class="ri-edit-line me-2"></i>
                    Editar Perfil
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('miembros.carnet', $miembro->id) }}" class="btn btn-outline-info w-100">
                    <i class="ri-qr-code-line me-2"></i>
                    Ver Carnet
                </a>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-success w-100" onclick="imprimirPerfil()">
                    <i class="ri-printer-line me-2"></i>
                    Imprimir
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-warning w-100" onclick="exportarPerfil()">
                    <i class="ri-download-line me-2"></i>
                    Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function imprimirPerfil() {
    window.print();
}

function exportarPerfil() {
    // Crear un objeto con los datos del perfil
    const perfilData = {
        nombre: '{{ $miembro->nombre }} {{ $miembro->apellido }}',
        email: '{{ $miembro->email }}',
        telefono: '{{ $miembro->telefono ?? "No especificado" }}',
        organizacion: '{{ $miembro->organizacion->nombre }}',
        tipo_membresia: '{{ $miembro->tipo_membresia }}',
        estado_membresia: '{{ $miembro->estado_membresia }}',
        numero_carnet: '{{ $miembro->numero_carnet }}',
        fecha_ingreso: '{{ $miembro->fecha_ingreso->format("d/m/Y") }}'
    };
    
    // Convertir a JSON y descargar
    const dataStr = JSON.stringify(perfilData, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = 'perfil-{{ $miembro->numero_carnet }}.json';
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
}
</script>
