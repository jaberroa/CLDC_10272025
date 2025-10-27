@extends('partials.layouts.master')

@section('title', 'Link Inválido | CLDCI')
@section('title-sub', 'Votación')
@section('pagetitle', 'Link de Votación Inválido')

@section('css')
<style>
    .error-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .error-card {
        max-width: 600px;
        text-align: center;
    }
    
    .error-icon {
        font-size: 5rem;
        color: #dc3545;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="error-container">
    <div class="error-card">
        <div class="card border-danger">
            <div class="card-body p-5">
                <i class="ri-error-warning-line error-icon"></i>
                
                <h2 class="mb-3">Link de Votación Inválido</h2>
                
                <div class="alert alert-danger">
                    <i class="ri-information-line me-2"></i>
                    {{ $mensaje ?? 'El link de votación que intentas usar no es válido o ha expirado.' }}
                </div>
                
                <div class="mt-4">
                    <h5 class="mb-3">Posibles razones:</h5>
                    <ul class="text-start">
                        <li class="mb-2">
                            <i class="ri-time-line text-warning me-2"></i>
                            <strong>El token ha expirado</strong> - Los links de votación tienen validez de 30 minutos
                        </li>
                        <li class="mb-2">
                            <i class="ri-check-line text-success me-2"></i>
                            <strong>Ya has votado</strong> - Cada link solo puede usarse una vez
                        </li>
                        <li class="mb-2">
                            <i class="ri-close-circle-line text-danger me-2"></i>
                            <strong>La elección ha finalizado</strong> - El periodo de votación ya terminó
                        </li>
                        <li class="mb-2">
                            <i class="ri-link-unlink-line text-muted me-2"></i>
                            <strong>Link modificado o corrupto</strong> - El link fue alterado
                        </li>
                    </ul>
                </div>
                
                <hr class="my-4">
                
                <h6 class="mb-3">¿Necesitas ayuda?</h6>
                <p class="text-muted">
                    Si crees que esto es un error, contacta al administrador de la elección 
                    para que te genere un nuevo link de votación.
                </p>
                
                <div class="mt-4">
                    <a href="{{ route('elecciones.index') }}" class="btn btn-primary">
                        <i class="ri-home-line me-2"></i>
                        Ir a Elecciones
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="ri-shield-check-line me-1"></i>
                Sistema de votación segura con tokens JWT de un solo uso
            </small>
        </div>
    </div>
</div>
@endsection


