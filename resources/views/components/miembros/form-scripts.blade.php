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

        // Validar que el estado de membresía no sea temporal (valor "temp_...")
        if (String(estadoMembresia).startsWith('temp_')) {
            e.preventDefault();
            showWarningToast('Debe guardar un Estado de Membresía válido (no temporal).');
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

// =====================================================================================
// Modales: agregar Estado/Tipo de Membresía en vivo (UI)
// =====================================================================================
document.addEventListener('DOMContentLoaded', function(){
    const btnAddEstado = document.getElementById('btnAddEstadoMembresia');
    const btnAddTipo = document.getElementById('btnAddTipoMembresia');
    const btnDeleteEstado = document.getElementById('btnDeleteEstadoMembresia');
    const btnDeleteTipo = document.getElementById('btnDeleteTipoMembresia');
    const estadoConfirmInput = document.getElementById('confirm_nombre_estado');
    const tipoConfirmInput = document.getElementById('confirm_nombre_tipo');
    const estadoConfirmHint = document.getElementById('estado_confirm_hint');
    const tipoConfirmHint = document.getElementById('tipo_confirm_hint');
    const estadoModalSelect = document.getElementById('estado_a_eliminar');
    const tipoModalSelect = document.getElementById('tipo_a_eliminar');
    if (btnAddEstado) btnAddEstado.addEventListener('click', () => new bootstrap.Modal(document.getElementById('modalNuevoEstado')).show());
    if (btnAddTipo) btnAddTipo.addEventListener('click', () => new bootstrap.Modal(document.getElementById('modalNuevoTipoMembresia')).show());

    function syncEstadoConfirmHint() {
        if (!estadoModalSelect) return;
        const selectedOption = estadoModalSelect.options[estadoModalSelect.selectedIndex];
        const chosenName = selectedOption ? selectedOption.textContent.trim() : '';
        if (estadoConfirmHint) {
            estadoConfirmHint.textContent = chosenName || 'Seleccione un estado';
        }
        if (estadoConfirmInput) {
            estadoConfirmInput.value = '';
            estadoConfirmInput.placeholder = chosenName ? `Escriba "${chosenName}"` : 'Escriba el nombre exacto';
        }
    }

    if (estadoModalSelect) {
        estadoModalSelect.addEventListener('change', syncEstadoConfirmHint);
    }

    if (btnDeleteEstado) btnDeleteEstado.addEventListener('click', () => {
        const sel = document.getElementById('estado_membresia_id');
        if (!sel || !estadoModalSelect) return;
        estadoModalSelect.innerHTML = '';
        [...sel.options].forEach(o => {
            if (o.value) {
                const op = document.createElement('option');
                op.value = o.value;
                op.textContent = o.textContent;
                estadoModalSelect.appendChild(op);
            }
        });
        estadoModalSelect.value = sel.value || '';
        syncEstadoConfirmHint();
        new bootstrap.Modal(document.getElementById('modalEliminarEstado')).show();
    });

    function syncTipoConfirmHint() {
        if (!tipoModalSelect) return;
        const selectedOption = tipoModalSelect.options[tipoModalSelect.selectedIndex];
        const chosenName = selectedOption ? selectedOption.textContent.trim() : '';
        if (tipoConfirmHint) {
            tipoConfirmHint.textContent = chosenName || 'Seleccione un tipo';
        }
        if (tipoConfirmInput) {
            tipoConfirmInput.value = '';
            tipoConfirmInput.placeholder = chosenName ? `Escriba "${chosenName}"` : 'Escriba el nombre exacto';
        }
    }

    if (tipoModalSelect) {
        tipoModalSelect.addEventListener('change', syncTipoConfirmHint);
    }

    if (btnDeleteTipo) btnDeleteTipo.addEventListener('click', () => {
        const sel = document.getElementById('tipo_membresia');
        if (!sel || !tipoModalSelect) return;
        tipoModalSelect.innerHTML = '';
        [...sel.options].forEach(o => {
            if (o.value) {
                const op = document.createElement('option');
                op.value = o.value;
                op.textContent = o.textContent;
                tipoModalSelect.appendChild(op);
            }
        });
        tipoModalSelect.value = sel.value || '';
        syncTipoConfirmHint();
        const modalEstado = bootstrap.Modal.getInstance(document.getElementById('modalEliminarEstado'));
        if (modalEstado) modalEstado.hide();
        // Abrir modal tipo (igual que Estado)
        new bootstrap.Modal(document.getElementById('modalEliminarTipo')).show();
    });

    // Confirmar eliminación de tipo (misma lógica que Estado)
    @if (Route::has('admin.tipos-membresia.delete'))
    const URL_DELETE_TIPO_BASE = @json(route('admin.tipos-membresia.delete', ['id' => 'PLACEHOLDER']));
    @endif
    const btnConfirmDeleteTipo = document.getElementById('confirm_delete_tipo');
    if (btnConfirmDeleteTipo) btnConfirmDeleteTipo.addEventListener('click', function(){
        const slug = (document.getElementById('tipo_a_eliminar')?.value)||'';
        const force = document.getElementById('force_delete_tipo')?.checked || false;
        if (!slug) { showWarningToast('Seleccione tipo'); return; }
        const confirmName = tipoConfirmInput ? tipoConfirmInput.value.trim() : '';
        if (!confirmName) { showWarningToast('Escriba el nombre exacto del tipo para confirmar.'); return; }
        const expectedName = tipoModalSelect?.options[tipoModalSelect.selectedIndex]?.textContent.trim() || '';
        if (!expectedName || confirmName !== expectedName) { showWarningToast('El nombre ingresado no coincide con el tipo seleccionado.'); return; }
        if (typeof URL_DELETE_TIPO_BASE === 'undefined') {
            showErrorToast('Ruta de eliminación no disponible');
            return;
        }
        const url = URL_DELETE_TIPO_BASE.replace('PLACEHOLDER', slug);
        fetch(url, {
            method:'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json' },
            body: new URLSearchParams({ confirm_name: confirmName, force: force ? '1':'0' })
        }).then(async res=>{
            const data = await res.json().catch(()=>({}));
            if (res.ok && data.success) {
                const sel = document.getElementById('tipo_membresia');
                const opt = [...sel.options].find(o=>o.value===slug);
                if (opt) opt.remove();
                sel.value='';
                baseToast('success','Tipo eliminado');
            } else if (res.status === 409 && data.needs_force) {
                showWarningToast(data.message || 'Hay dependencias. Active Forzar.');
            } else {
                showErrorToast(data.message || 'No se pudo eliminar el tipo');
            }
        }).catch(()=> showErrorToast('Error de conexión'))
        .finally(()=>{
            try { new bootstrap.Modal(document.getElementById('modalEliminarTipo')).hide(); } catch(_){}
            document.querySelectorAll('.modal-backdrop').forEach(b=>b.remove());
        });
    });

    const btnGuardarEstado = document.getElementById('btnGuardarEstado');
    if (btnGuardarEstado) btnGuardarEstado.addEventListener('click', function(){
        const nombre = (document.getElementById('estado_nombre')?.value || '').trim();
        const descripcion = (document.getElementById('estado_descripcion')?.value || '').trim();
        if (!nombre) { showWarningToast('Ingrese un nombre'); return; }
        fetch(`{{ route('admin.estados-membresia.create') }}`, {
            method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ nombre, descripcion })
        }).then(async res=>{
            const data = await res.json().catch(()=>({}));
            if (res.ok && data.success) {
                const select = document.getElementById('estado_membresia_id');
                const opt = document.createElement('option');
                opt.value = data.estado.id;
                opt.textContent = data.estado.nombre;
                select.appendChild(opt);
                select.value = String(data.estado.id);
                try { new bootstrap.Modal(document.getElementById('modalNuevoEstado')).hide(); } catch(_){}
                baseToast('success', 'Estado agregado');
            } else {
                showErrorToast(data.message || 'No se pudo crear el estado');
            }
        }).catch(()=> showErrorToast('Error de conexión'));
    });

    const btnGuardarTipo = document.getElementById('btnGuardarTipo');
    if (btnGuardarTipo) btnGuardarTipo.addEventListener('click', function(){
        const nombre = (document.getElementById('tipo_nombre')?.value || '').trim();
        if (!nombre) { showWarningToast('Ingrese un nombre'); return; }
        fetch(`{{ route('admin.tipos-membresia.create') }}`, {
            method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},
            body: JSON.stringify({ nombre })
        }).then(async res=>{
            const data = await res.json().catch(()=>({}));
            if (res.ok && data.success) {
                const select = document.getElementById('tipo_membresia');
                const opt = document.createElement('option');
                opt.value = data.tipo.slug;
                opt.textContent = data.tipo.nombre;
                select.appendChild(opt);
                select.value = data.tipo.slug;
                try { new bootstrap.Modal(document.getElementById('modalNuevoTipoMembresia')).hide(); } catch(_){}
                baseToast('success', 'Tipo agregado');
            } else {
                showErrorToast(data.message || 'No se pudo crear el tipo');
            }
        }).catch(()=> showErrorToast('Error de conexión'));
    });

    // Confirmar eliminación de estado (llama endpoint admin)
    const btnConfirmEliminarEstado = document.getElementById('btnConfirmEliminarEstado');
    if (btnConfirmEliminarEstado) btnConfirmEliminarEstado.addEventListener('click', function(){
        const estadoId = (document.getElementById('estado_a_eliminar')?.value)||'';
        const force = document.getElementById('force_delete_estado')?.checked || false;
        if (!estadoId) { showWarningToast('Seleccione estado'); return; }
        const confirmName = estadoConfirmInput ? estadoConfirmInput.value.trim() : '';
        if (!confirmName) { showWarningToast('Escriba el nombre exacto del estado para confirmar.'); return; }
        const expectedName = estadoModalSelect?.options[estadoModalSelect.selectedIndex]?.textContent.trim() || '';
        if (!expectedName || confirmName !== expectedName) { showWarningToast('El nombre ingresado no coincide con el estado seleccionado.'); return; }
        fetch(`{{ route('admin.estados-membresia.delete', ['id' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', estadoId), {
            method:'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json' },
            body: new URLSearchParams({ confirm_name: confirmName, force: force ? '1':'0' })
        }).then(async res=>{
            const data = await res.json().catch(()=>({}));
            if (res.ok && data.success) {
                // Remover opción del select principal
                const sel = document.getElementById('estado_membresia_id');
                const opt = [...sel.options].find(o=>o.value==estadoId);
                if (opt) opt.remove();
                sel.value='';
                baseToast('success','Estado eliminado');
                try { new bootstrap.Modal(document.getElementById('modalEliminarEstado')).hide(); } catch(_){}
            } else {
                showErrorToast(data.message || 'No se pudo eliminar');
            }
        }).catch(()=> showErrorToast('Error de conexión'));
    });
});
</script>