<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalidadSelect = document.getElementById('modalidad');
    const enlaceVirtualContainer = document.getElementById('enlace-virtual-container');
    const enlaceVirtualInput = document.getElementById('enlace_virtual');

    modalidadSelect.addEventListener('change', function() {
        if (this.value === 'virtual' || this.value === 'hibrida') {
            enlaceVirtualContainer.style.display = 'block';
            enlaceVirtualInput.required = true;
        } else {
            enlaceVirtualContainer.style.display = 'none';
            enlaceVirtualInput.required = false;
            enlaceVirtualInput.value = '';
        }
    });

    // Validación de fechas
    const fechaConvocatoria = document.getElementById('fecha_convocatoria');
    const fechaAsamblea = document.getElementById('fecha_asamblea');

    fechaConvocatoria.addEventListener('change', function() {
        const convocatoria = new Date(this.value);
        const asamblea = new Date(fechaAsamblea.value);
        
        if (asamblea <= convocatoria) {
            fechaAsamblea.value = new Date(convocatoria.getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
        }
    });

    fechaAsamblea.addEventListener('change', function() {
        const convocatoria = new Date(fechaConvocatoria.value);
        const asamblea = new Date(this.value);
        
        if (asamblea <= convocatoria) {
            alert('La fecha de la asamblea debe ser posterior a la fecha de convocatoria');
            this.value = new Date(convocatoria.getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
        }
    });

    // Inicializar estado del enlace virtual si ya hay un valor
    if (modalidadSelect.value === 'virtual' || modalidadSelect.value === 'hibrida') {
        enlaceVirtualContainer.style.display = 'block';
        enlaceVirtualInput.required = true;
    }
});
</script>

