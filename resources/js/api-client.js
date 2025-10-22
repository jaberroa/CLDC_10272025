/**
 * API Client for CLDCI System
 * Handles AJAX requests to the Laravel API
 */

class CldciApiClient {
    constructor() {
        this.baseUrl = '/api';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Make a GET request to the API
     */
    async get(endpoint, params = {}) {
        const url = new URL(`${this.baseUrl}${endpoint}`, window.location.origin);
        
        // Add query parameters
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined) {
                url.searchParams.append(key, params[key]);
            }
        });

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API GET Error:', error);
            throw error;
        }
    }

    /**
     * Make a POST request to the API
     */
    async post(endpoint, data = {}) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API POST Error:', error);
            throw error;
        }
    }

    /**
     * Make a PUT request to the API
     */
    async put(endpoint, data = {}) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API PUT Error:', error);
            throw error;
        }
    }

    /**
     * Make a DELETE request to the API
     */
    async delete(endpoint) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API DELETE Error:', error);
            throw error;
        }
    }

    // Dashboard API Methods
    async getDashboardStats() {
        return await this.get('/dashboard/estadisticas');
    }

    async getMiembrosPorTipo() {
        return await this.get('/dashboard/miembros-por-tipo');
    }

    async getOrganizacionesPorTipo() {
        return await this.get('/dashboard/organizaciones-por-tipo');
    }

    async getTransaccionesRecientes() {
        return await this.get('/dashboard/transacciones-recientes');
    }

    async getAsambleasProximas() {
        return await this.get('/dashboard/asambleas-proximas');
    }

    async getEleccionesProximas() {
        return await this.get('/dashboard/elecciones-proximas');
    }

    async getMiembrosActivos(tipo = 'asambleas', limit = 5) {
        return await this.get('/dashboard/miembros-activos', { tipo, limit });
    }

    async getResumenFinanciero() {
        return await this.get('/dashboard/resumen-financiero');
    }

    // Miembros API Methods
    async getMiembros(filters = {}) {
        return await this.get('/miembros', filters);
    }

    async getMiembro(id) {
        return await this.get(`/miembros/${id}`);
    }

    async getMiembrosEstadisticas() {
        return await this.get('/miembros/estadisticas/estadisticas');
    }

    async searchMiembros(query) {
        return await this.get('/miembros/search/search', { q: query });
    }

    async getOrganizaciones() {
        return await this.get('/miembros/filtros/organizaciones');
    }

    async getEstadosMembresia() {
        return await this.get('/miembros/filtros/estados-membresia');
    }
}

// Create global instance
window.CldciApi = new CldciApiClient();

// Utility functions for common operations
window.CldciUtils = {
    /**
     * Show loading spinner
     */
    showLoading(element) {
        if (element) {
            element.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Cargando...</span></div>';
        }
    },

    /**
     * Hide loading spinner
     */
    hideLoading(element, originalContent) {
        if (element) {
            element.innerHTML = originalContent;
        }
    },

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        // Add to toast container
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('es-DO', {
            style: 'currency',
            currency: 'DOP'
        }).format(amount);
    },

    /**
     * Format date
     */
    formatDate(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        };
        return new Intl.DateTimeFormat('es-DO', { ...defaultOptions, ...options }).format(new Date(date));
    }
};


