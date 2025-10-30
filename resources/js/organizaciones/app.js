/**
 * MÓDULO ORGANIZACIONES - JAVASCRIPT
 * Funcionalidades específicas para el módulo de organizaciones
 */

// Configuración global
window.OrganizacionesModule = {
    // Configuración
    config: {
        apiUrl: '/api/organizaciones',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    },

    // Inicialización
    init() {
        this.initEventListeners();
        this.initFormValidation();
        this.initDataTables();
        this.initTooltips();
    },

    // Event listeners
    initEventListeners() {
        // Búsqueda en tiempo real
        const searchInput = document.getElementById('buscar');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }

        // Filtros automáticos
        const filterSelects = document.querySelectorAll('#filtros-form select');
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                document.getElementById('filtros-form').submit();
            });
        });

        // Selección múltiple
        this.initBulkSelection();

        // Exportar
        const exportBtn = document.getElementById('exportar-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => this.exportData());
        }
    },

    // Validación de formularios
    initFormValidation() {
        const form = document.getElementById('organizacion-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
            });

            // Validación en tiempo real
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearFieldError(input));
            });
        }
    },

    // Validar formulario completo
    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    },

    // Validar campo individual
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        // Validaciones específicas
        switch (fieldName) {
            case 'nombre':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'El nombre debe tener al menos 3 caracteres';
                }
                break;
            case 'codigo':
                if (!/^[A-Z0-9-_]+$/i.test(value)) {
                    isValid = false;
                    errorMessage = 'El código solo puede contener letras, números, guiones y guiones bajos';
                }
                break;
            case 'email':
                if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Ingrese un correo electrónico válido';
                }
                break;
            case 'telefono':
                if (value && !/^[\d\s\-\(\)\+]+$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Ingrese un número de teléfono válido';
                }
                break;
        }

        // Mostrar error
        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }

        return isValid;
    },

    // Mostrar error en campo
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    },

    // Limpiar error de campo
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.textContent = '';
        }
    },

    // Inicializar selección múltiple
    initBulkSelection() {
        const selectAllCheckbox = document.getElementById('select-all');
        const organizacionCheckboxes = document.querySelectorAll('.organizacion-checkbox');
        const bulkDeleteBtn = document.getElementById('eliminar-seleccionados-btn');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', () => {
                organizacionCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                this.updateBulkActions();
            });
        }

        organizacionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateBulkActions();
                this.updateSelectAllCheckbox();
            });
        });

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', () => this.bulkDelete());
        }
    },

    // Actualizar acciones masivas
    updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.organizacion-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('eliminar-seleccionados-btn');
        
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = checkedBoxes.length === 0;
        }
    },

    // Actualizar checkbox "Seleccionar todo"
    updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('select-all');
        const organizacionCheckboxes = document.querySelectorAll('.organizacion-checkbox');
        const checkedBoxes = document.querySelectorAll('.organizacion-checkbox:checked');
        
        if (selectAllCheckbox) {
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === organizacionCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    },

    // Eliminar múltiples organizaciones
    async bulkDelete() {
        const checkedBoxes = document.querySelectorAll('.organizacion-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (ids.length === 0) return;
        
        if (!confirm(`¿Está seguro de que desea eliminar ${ids.length} organización(es) seleccionada(s)?`)) {
            return;
        }

        try {
            const response = await fetch('/organizaciones/bulk-delete', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ selected_ids: ids })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast('error', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('error', 'Error al eliminar las organizaciones');
        }
    },

    // Cambiar estado de organización
    async cambiarEstado(organizacionId, nuevoEstado) {
        const accion = nuevoEstado === 'activa' ? 'activar' : 'desactivar';
        
        if (!confirm(`¿Está seguro de que desea ${accion} esta organización?`)) {
            return;
        }

        try {
            const response = await fetch(`/organizaciones/${organizacionId}/${accion}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast('error', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('error', 'Error al cambiar el estado de la organización');
        }
    },

    // Eliminar organización individual
    async eliminarOrganizacion(organizacionId) {
        if (!confirm('¿Está seguro de que desea eliminar esta organización? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            const response = await fetch(`/organizaciones/${organizacionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast('error', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('error', 'Error al eliminar la organización');
        }
    },

    // Búsqueda en tiempo real
    async performSearch(query) {
        if (query.length < 2) return;

        try {
            const response = await fetch(`/api/organizaciones/buscar?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            // Actualizar resultados (implementar según necesidad)
            console.log('Resultados de búsqueda:', data);
        } catch (error) {
            console.error('Error en búsqueda:', error);
        }
    },

    // Exportar datos
    exportData() {
        const form = document.getElementById('filtros-form');
        if (form) {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            window.open(`/organizaciones/exportar?${params.toString()}`, '_blank');
        }
    },

    // Inicializar DataTables (si se usa)
    initDataTables() {
        // Implementar si se necesita DataTables
    },

    // Inicializar tooltips
    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    // Mostrar toast
    showToast(type, message) {
        // Implementar sistema de toast
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remover toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },

    // Crear contenedor de toasts
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
};

// Funciones globales para compatibilidad
window.cambiarEstado = (organizacionId, nuevoEstado) => {
    OrganizacionesModule.cambiarEstado(organizacionId, nuevoEstado);
};

window.eliminarOrganizacion = (organizacionId) => {
    OrganizacionesModule.eliminarOrganizacion(organizacionId);
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    OrganizacionesModule.init();
});

