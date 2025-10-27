@props(['miembro'])

<div class="card text-center">
    <div class="card-header">
        <h5 class="card-title">Perfil Social</h5>
    </div>
    <div class="card-body p-4">
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-primary icon-btn rounded">
                <i class="ri-facebook-fill fs-18"></i>
            </button>
            <button type="button" class="btn btn-info icon-btn rounded">
                <i class="ri-twitter-line fs-18"></i>
            </button>
            <button type="button" class="btn btn-danger icon-btn rounded">
                <i class="ri-instagram-line fs-18"></i>
            </button>
            <button type="button" class="btn btn-dark icon-btn rounded">
                <i class="ri-linkedin-line fs-18"></i>
            </button>
        </div>
        <div class="mt-3">
            <small class="text-muted">Conecta con {{ $miembro->nombre }} en redes sociales</small>
        </div>
    </div>
</div>


