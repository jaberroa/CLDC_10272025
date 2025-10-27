@props(['tipo'])

<div class="d-flex align-items-center tipo-membresia-container">
    <div class="flex-shrink-0 me-2">
        <div class="avatar-xs bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
            <i class="ri-user-star-line text-primary fs-10"></i>
        </div>
    </div>
    <div class="flex-grow-1">
        <span class="fw-semibold text-dark">{{ ucfirst($tipo ?? 'Miembro') }}</span>
    </div>
</div>
