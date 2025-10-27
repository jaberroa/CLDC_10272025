<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalidadSelect = document.getElementById('modalidad');
    const enlaceVirtualContainer = document.getElementById('enlace-virtual-container');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');

    // Mostrar/ocultar campo de enlace virtual según modalidad
    function toggleEnlaceVirtual() {
        if (modalidadSelect.value === 'virtual' || modalidadSelect.value === 'mixta') {
            enlaceVirtualContainer.style.display = 'block';
        } else {
            enlaceVirtualContainer.style.display = 'none';
        }
    }

    // Validar que fecha de fin sea posterior a fecha de inicio
    function validateFechas() {
        if (fechaInicioInput.value && fechaFinInput.value) {
            const fechaInicio = new Date(fechaInicioInput.value);
            const fechaFin = new Date(fechaFinInput.value);
            
            if (fechaFin <= fechaInicio) {
                fechaFinInput.setCustomValidity('La fecha de finalización debe ser posterior a la fecha de inicio');
            } else {
                fechaFinInput.setCustomValidity('');
            }
        }
    }

    // Event listeners
    modalidadSelect.addEventListener('change', toggleEnlaceVirtual);
    fechaInicioInput.addEventListener('change', validateFechas);
    fechaFinInput.addEventListener('change', validateFechas);

    // Inicializar estado
    toggleEnlaceVirtual();

    // Validación en tiempo real
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validar fechas antes de enviar
            if (fechaInicioInput.value && fechaFinInput.value) {
                const fechaInicio = new Date(fechaInicioInput.value);
                const fechaFin = new Date(fechaFinInput.value);
                
                if (fechaFin <= fechaInicio) {
                    e.preventDefault();
                    alert('La fecha de finalización debe ser posterior a la fecha de inicio');
                    return false;
                }
            }

            // Validar que si es virtual o mixta, tenga enlace
            if ((modalidadSelect.value === 'virtual' || modalidadSelect.value === 'mixta') && 
                !document.getElementById('enlace_virtual').value.trim()) {
                e.preventDefault();
                alert('Debe proporcionar un enlace virtual para modalidades virtual o mixta');
                return false;
            }
        });
    }
});
</script>

