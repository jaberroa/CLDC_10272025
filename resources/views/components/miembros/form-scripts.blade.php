<script>
document.addEventListener('DOMContentLoaded', function() {
    // Los selects funcionan sin Select2

    // Vista previa de la foto con validación de tamaño
    $('#foto').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (1MB = 1024 * 1024 bytes)
            const maxSize = 1024 * 1024; // 1MB
            if (file.size > maxSize) {
                alert('El archivo es demasiado grande. El tamaño máximo permitido es 1MB.');
                $(this).val(''); // Limpiar el input
                return;
            }
            
            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Formato de archivo no permitido. Solo se permiten JPG, PNG y GIF.');
                $(this).val(''); // Limpiar el input
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.avatar-lg').html(`<img src="${e.target.result}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">`);
            };
            reader.readAsDataURL(file);
        }
    });

    // Validación del formulario
    $('#miembro-form').on('submit', function(e) {
        const nombre = $('#nombre').val().trim();
        const apellido = $('#apellido').val().trim();
        const cedula = $('#cedula').val().trim();
        const email = $('#email').val().trim();
        const tipoMembresia = $('#tipo_membresia').val();
        const estadoMembresia = $('#estado_membresia').val();
        const organizacionId = $('#organizacion_id').val();
        const fechaIngreso = $('#fecha_ingreso').val();

        if (!nombre || !apellido || !cedula || !email || !tipoMembresia || !estadoMembresia || !organizacionId || !fechaIngreso) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios.');
            return false;
        }

        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor, ingrese un email válido.');
            return false;
        }

        // Validar fecha de ingreso
        const fechaIngresoDate = new Date(fechaIngreso);
        const hoy = new Date();
        if (fechaIngresoDate > hoy) {
            e.preventDefault();
            alert('La fecha de ingreso no puede ser futura.');
            return false;
        }
    });

    // Auto-generar email basado en nombre y apellido (solo en creación)
    @if(!isset($miembro))
    $('#nombre, #apellido').on('blur', function() {
        const nombre = $('#nombre').val().trim().toLowerCase();
        const apellido = $('#apellido').val().trim().toLowerCase();
        
        if (nombre && apellido && !$('#email').val()) {
            const emailSugerido = `${nombre}.${apellido}@cldci.org`;
            $('#email').val(emailSugerido);
        }
    });
    @endif
});
</script>