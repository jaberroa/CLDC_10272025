@extends('partials.layouts.master')

@section('title', 'Agregar Organización | CLDCI')
@section('title-sub', 'Gestión de Organizaciones')
@section('pagetitle', 'Agregar Nueva Organización')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/organizaciones-create-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/organizaciones/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header organizaciones-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('organizaciones.alt') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-building-add-line"></i>
                            Agregar Nueva Organización
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información de la organización para registrarla en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-organizaciones.form
                    :tipos-organizacion="$tiposOrganizacion"
                    submit-label="Guardar Organización"
                    submit-icon="ri-save-line"
                    submit-class="btn btn-primary"
                    cancel-label="Cancelar"
                    cancel-icon="ri-close-line"
                    cancel-class="btn btn-outline-secondary"
                    align-buttons="start"
                />
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.getElementById('organizacion-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validaciones básicas
            const nombre = document.getElementById('nombre');
            const codigo = document.getElementById('codigo');
            const tipo = document.getElementById('tipo');
            
            if (!nombre.value.trim()) {
                e.preventDefault();
                nombre.focus();
                alert('El nombre es requerido');
                return false;
            }
            
            if (!codigo.value.trim()) {
                e.preventDefault();
                codigo.focus();
                alert('El código es requerido');
                return false;
            }
            
            if (!tipo.value) {
                e.preventDefault();
                tipo.focus();
                alert('El tipo de organización es requerido');
                return false;
            }
        });
    }
    
    // Auto-generar código basado en el nombre
    const nombreInput = document.getElementById('nombre');
    const codigoInput = document.getElementById('codigo');
    
    if (nombreInput && codigoInput) {
        nombreInput.addEventListener('input', function() {
            if (!codigoInput.value) {
                const nombre = this.value.trim();
                if (nombre) {
                    // Generar código automático
                    const codigo = nombre
                        .toUpperCase()
                        .replace(/[^A-Z0-9\s]/g, '')
                        .replace(/\s+/g, '-')
                        .substring(0, 20);
                    codigoInput.value = codigo;
                }
            }
        });
    }
    
    // Preview de imagen
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreview.src = e.target.result;
                    logoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection