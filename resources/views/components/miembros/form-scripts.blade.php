<script>
document.addEventListener('DOMContentLoaded', function() {
    // Los selects funcionan sin Select2

    // Vista previa de la foto con validación de tamaño
    $('#foto').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                showErrorToast('El archivo es demasiado grande. Tamaño máximo: 5MB.');
                $(this).val(''); // Limpiar el input
                return;
            }
            
            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showErrorToast('Formato no permitido. Solo JPG, PNG y GIF.');
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
        const estadoMembresia = $('#estado_membresia_id').val();
        const organizacionId = $('#organizacion_id').val();
        const fechaIngreso = $('#fecha_ingreso').val();

        if (!nombre || !apellido || !cedula || !email || !tipoMembresia || !estadoMembresia || !organizacionId || !fechaIngreso) {
            e.preventDefault();
            showWarningToast('Por favor, complete todos los campos obligatorios.');
            return false;
        }

        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showWarningToast('Por favor, ingrese un email válido.');
            return false;
        }

        // Validar fecha de ingreso
        const fechaIngresoDate = new Date(fechaIngreso);
        const hoy = new Date();
        if (fechaIngresoDate > hoy) {
            e.preventDefault();
            showWarningToast('La fecha de ingreso no puede ser futura.');
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

// =====================================================================================
// Formatos dinámicos RD: Cédula (000-0000000-0) y Teléfono ((809) 123-4567)
// =====================================================================================
(function() {
    const cedulaInput = document.getElementById('cedula');
    const telefonoInput = document.getElementById('telefono');

    function onlyDigits(value) { return (value || '').replace(/\D+/g, ''); }

    function formatCedulaRD(value) {
        const d = onlyDigits(value).slice(0, 11);
        if (d.length <= 3) return d;
        if (d.length <= 10) return `${d.slice(0,3)}-${d.slice(3)}`;
        return `${d.slice(0,3)}-${d.slice(3,10)}-${d.slice(10)}`;
    }

    function formatTelefonoRD(value) {
        let d = onlyDigits(value);
        if (d.length === 11 && d.startsWith('1')) d = d.slice(1);
        d = d.slice(0, 10);
        if (d.length <= 3) return d;
        if (d.length <= 6) return `(${d.slice(0,3)}) ${d.slice(3)}`;
        return `(${d.slice(0,3)}) ${d.slice(3,6)}-${d.slice(6)}`;
    }

    function attachFormatter(el, formatter) {
        if (!el) return;
        el.addEventListener('input', (e) => { e.target.value = formatter(e.target.value); });
        el.addEventListener('blur', (e) => { e.target.value = formatter(e.target.value); });
        el.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            e.target.value = formatter(text);
        });
        // Formateo inicial
        el.value = formatter(el.value);
    }

    attachFormatter(cedulaInput, formatCedulaRD);
    attachFormatter(telefonoInput, formatTelefonoRD);
})();

// =====================================================================================
// Toasts con SweetAlert2 (estilo consistente con la UI de Miembros)
// =====================================================================================
// Cargar SweetAlert2 si no está presente
if (typeof window.Swal === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(script);
}

function baseToast(icon, title) {
    // Esperar a que SweetAlert esté disponible
    const run = () => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'shadow-sm rounded-3',
                title: 'fw-semibold'
            }
        });
    };
    if (typeof Swal === 'undefined') {
        setTimeout(run, 200);
    } else {
        run();
    }
}

function showErrorToast(message) { baseToast('error', message); }
function showWarningToast(message) { baseToast('warning', message); }
</script>