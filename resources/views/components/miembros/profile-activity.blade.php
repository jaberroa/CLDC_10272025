@props(['actividadReciente'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Actividad Reciente</h5>
    </div>
    <div class="card-body">
        <div class="timeline2 icon-timeline">
            <ul>
                @forelse($actividadReciente as $actividad)
                <li class="box">
                    <span class="bg-{{ $actividad['color'] }}">
                        <i class="{{ $actividad['icono'] }}"></i>
                    </span>
                    <div class="text-muted float-end fs-13">{{ $actividad['fecha']->format('d M Y') }}</div>
                    <div class="title">{{ $actividad['titulo'] }}</div>
                    <div class="info">{{ $actividad['descripcion'] }}</div>
                </li>
                @empty
                <li class="box">
                    <span class="bg-secondary">
                        <i class="ri-information-line"></i>
                    </span>
                    <div class="text-muted float-end fs-13">{{ now()->format('d M Y') }}</div>
                    <div class="title">Sin actividad reciente</div>
                    <div class="info">No hay registros de actividad reciente para mostrar.</div>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


