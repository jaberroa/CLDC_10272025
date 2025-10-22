@extends('partials.layouts.master')

@section('title', 'Carnet Digital - Seleccionar Plantilla')

@section('css')
<link rel="stylesheet" href="{{ vite_asset('resources/css/carnet/app.css') }}">
@endsection

@section('content')
<div class="carnet-system">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">
                            <i class="ri-qr-code-line me-2"></i>
                            Carnet Digital
                        </h2>
                        <p class="text-muted mb-0">Selecciona una plantilla para el carnet de {{ $miembro->nombre_completo }}</p>
                    </div>
                    <div>
                        <a href="{{ route('miembros.profile', $miembro->id) }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Volver al Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plantillas disponibles -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">Plantillas Disponibles</h4>
                <div class="carnet-templates-container">
                    <div class="row">
                        @foreach($templates as $template)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card template-card h-100" data-template-id="{{ $template->id }}">
                                <div class="card-body">
                                    <!-- Vista previa de la plantilla -->
                                    <div class="template-preview mb-3">
                                        <div class="carnet-preview {{ str_replace('.', '-', $template->template_path) }}">
                                            <div class="carnet-base" style="transform: scale(0.5); transform-origin: top left; width: 200px; height: 125px;">
                                                <!-- Header del carnet -->
                                                <div class="carnet-header" style="background: {{ $template->configuracion_default['color_primario'] ?? '#008080' }}; color: white; padding: 10px; display: flex; align-items: center; gap: 8px;">
                                                    <img src="{{ $miembro->foto_url ? asset($miembro->foto_url) : asset('assets/images/default-avatar.png') }}" 
                                                         alt="Foto" class="carnet-photo" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                                    
                                                    <div class="carnet-info">
                                                        <div class="carnet-text nombre" style="font-size: 10px; font-weight: bold; margin: 0;">{{ Str::limit($miembro->nombre_completo, 20) }}</div>
                                                        <div class="carnet-text profesion" style="font-size: 8px; margin: 0;">{{ Str::limit($miembro->profesion, 15) }}</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Body del carnet -->
                                                <div class="carnet-body" style="background: {{ $template->configuracion_default['color_fondo'] ?? '#ffffff' }}; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                                                    <div class="carnet-qr-section">
                                                        <div class="carnet-qr" style="width: 25px; height: 25px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 8px;">QR</div>
                                                    </div>
                                                    
                                                    <div class="carnet-details" style="text-align: right;">
                                                        <div class="carnet-number" style="font-size: 8px; font-weight: bold;">{{ $miembro->numero_carnet }}</div>
                                                        <div class="carnet-status-badge activa" style="background: {{ $template->configuracion_default['color_primario'] ?? '#008080' }}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 6px; display: inline-block;">ACTIVA</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información de la plantilla -->
                                    <div class="template-info">
                                        <h5 class="card-title">{{ $template->nombre }}</h5>
                                        <p class="card-text text-muted">{{ $template->descripcion }}</p>
                                        
                                        @if($personalizado && $personalizado->template_id == $template->id)
                                        <div class="alert alert-info alert-sm">
                                            <i class="ri-information-line me-1"></i>
                                            Ya tienes una personalización guardada
                                        </div>
                                        @endif
                                        
                                        <div class="d-grid">
                                            <a href="{{ route('carnet.editor', [$miembro->id, $template->id]) }}" 
                                               class="btn btn-primary">
                                                <i class="ri-edit-line me-1"></i> 
                                                {{ $personalizado && $personalizado->template_id == $template->id ? 'Editar' : 'Personalizar' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="ri-information-line me-2"></i>
                            Información del Sistema de Carnet
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6>🎨 Personalización</h6>
                                <p class="text-muted small">
                                    Cambia colores, fuentes, tamaños y estilos de texto según tus preferencias.
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>📱 Código QR</h6>
                                <p class="text-muted small">
                                    Genera códigos QR únicos para cada miembro con información personalizada.
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>📄 Exportación</h6>
                                <p class="text-muted small">
                                    Exporta tu carnet como PDF de alta calidad para impresión o uso digital.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ vite_asset('resources/js/carnet/app.js') }}"></script>
@endsection
