@props(['estado'])

<div class="estado-membresia-container">
    @if($estado)
        <span class="fw-semibold" style="font-size: 0.875rem; color: {{ $estado->color ?? '#6c757d' }};">
            {{ ucfirst($estado->nombre) }}
        </span>
    @else
        <span class="fw-semibold text-secondary" style="font-size: 0.875rem;">
            Sin Estado
        </span>
    @endif
</div>
