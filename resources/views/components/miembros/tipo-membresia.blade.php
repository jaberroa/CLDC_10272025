@props(['tipo'])

<div class="tipo-membresia-container">
    <span class="fw-semibold text-dark">
        @if($tipo && is_object($tipo))
            {{ ucfirst($tipo->nombre) }}
        @else
            {{ ucfirst($tipo ?? 'Miembro') }}
        @endif
    </span>
</div>
