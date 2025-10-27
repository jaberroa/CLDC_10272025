@extends('partials.layouts.master')

@section('title', 'Resultados - ' . $eleccion->titulo . ' | CLDCI')
@section('title-sub', 'Resultados de Elección')
@section('pagetitle', 'Resultados: ' . $eleccion->titulo)

@section('css')
<style>
    .resultado-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        background: white;
    }
    
    .resultado-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .ganador {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
    }
    
    .ganador .badge-ganador {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .votos-barra {
        height: 30px;
        background: #e9ecef;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }
    
    .votos-progreso {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.5s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .ganador .votos-progreso {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    }
</style>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('elecciones.index') }}" class="btn btn-soft-secondary">
            <i class="ri-arrow-left-line me-1"></i>
            Volver a Elecciones
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <x-miembros.stat-card
            title="Total de Votos"
            :value="number_format($estadisticas['total_votos'])"
            icon="ri-checkbox-circle-line"
            background="bg-success-subtle"
            icon-background="bg-success"
        />
    </div>
    <div class="col-md-4">
        <x-miembros.stat-card
            title="Candidatos"
            :value="number_format($estadisticas['total_candidatos'])"
            icon="ri-user-line"
            background="bg-primary-subtle"
            icon-background="bg-primary"
        />
    </div>
    <div class="col-md-4">
        <x-miembros.stat-card
            title="Participación"
            :value="$estadisticas['participacion'] . '%'"
            icon="ri-pie-chart-line"
            background="bg-info-subtle"
            icon-background="bg-info"
        />
    </div>
</div>

<!-- Información de la Elección -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-2">{{ $eleccion->titulo }}</h4>
                        @if($eleccion->descripcion)
                            <p class="text-muted mb-3">{{ $eleccion->descripcion }}</p>
                        @endif
                        <div class="d-flex gap-3">
                            <span class="badge bg-soft-info text-info">
                                <i class="ri-building-line me-1"></i>
                                {{ $eleccion->organizacion->nombre ?? 'N/A' }}
                            </span>
                            <span class="badge bg-soft-primary text-primary">
                                <i class="ri-calendar-line me-1"></i>
                                {{ $eleccion->fecha_inicio->format('d/m/Y') }} - {{ $eleccion->fecha_fin->format('d/m/Y') }}
                            </span>
                            <span class="badge 
                                @if($eleccion->estado == 'programada') bg-soft-warning text-warning
                                @elseif($eleccion->estado == 'activa') bg-soft-success text-success
                                @elseif($eleccion->estado == 'finalizada') bg-soft-secondary text-secondary
                                @else bg-soft-danger text-danger
                                @endif">
                                <i class="ri-flag-line me-1"></i>
                                {{ ucfirst($eleccion->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resultados -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-trophy-line me-2"></i>
                    Resultados
                </h5>
            </div>
            <div class="card-body">
                @if($resultados->isEmpty())
                    <div class="text-center py-5">
                        <i class="ri-inbox-line fs-1 text-muted d-block mb-3"></i>
                        <h5>No hay resultados disponibles</h5>
                        <p class="text-muted">Aún no se han registrado votos para esta elección.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($resultados as $index => $resultado)
                            <div class="col-12">
                                <div class="resultado-card {{ $index === 0 ? 'ganador' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-1">
                                                @if($index === 0)
                                                    <i class="ri-trophy-fill text-warning me-2"></i>
                                                @endif
                                                {{ $resultado['candidato'] }}
                                            </h5>
                                            <small class="text-muted">{{ $resultado['cargo'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            @if($index === 0)
                                                <span class="badge-ganador">
                                                    <i class="ri-medal-line me-1"></i>
                                                    Ganador
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="votos-barra mb-2">
                                        @php
                                            $totalVotos = $estadisticas['total_votos'] > 0 ? $estadisticas['total_votos'] : 1;
                                            $porcentaje = ($resultado['votos'] / $totalVotos) * 100;
                                        @endphp
                                        <div class="votos-progreso" style="width: {{ $porcentaje }}%">
                                            {{ number_format($porcentaje, 1) }}%
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            <i class="ri-checkbox-circle-line me-1"></i>
                                            {{ number_format($resultado['votos']) }} {{ $resultado['votos'] == 1 ? 'voto' : 'votos' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($eleccion->estado == 'activa')
<div class="alert alert-info mt-4">
    <i class="ri-information-line me-2"></i>
    <strong>Nota:</strong> Los resultados se actualizan en tiempo real mientras la elección esté activa.
</div>
@endif
@endsection

@section('js')
<script>
    // Auto-actualizar resultados cada 30 segundos si la elección está activa
    @if($eleccion->estado == 'activa')
        setInterval(() => {
            location.reload();
        }, 30000); // 30 segundos
    @endif
</script>
@endsection


