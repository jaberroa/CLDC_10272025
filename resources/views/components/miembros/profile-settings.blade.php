@props(['miembro'])

<div class="card">
    <div class="card-body">
        <form>
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="fullName" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="fullName" value="{{ $miembro->nombre }} {{ $miembro->apellido }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" value="{{ $miembro->email }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="phone" value="{{ $miembro->telefono ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="organization" class="form-label">Organización</label>
                    <input type="text" class="form-control" id="organization" value="{{ $miembro->organizacion->nombre }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="membershipType" class="form-label">Tipo de Membresía</label>
                    <input type="text" class="form-control" id="membershipType" value="{{ ucfirst($miembro->tipo_membresia) }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="membershipStatus" class="form-label">Estado de Membresía</label>
                    <input type="text" class="form-control" id="membershipStatus" value="{{ ucfirst($miembro->estado_membresia) }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="cardNumber" class="form-label">Número de Carnet</label>
                    <input type="text" class="form-control" id="cardNumber" value="{{ $miembro->numero_carnet }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="joinDate" class="form-label">Fecha de Ingreso</label>
                    <input type="text" class="form-control" id="joinDate" value="{{ $miembro->fecha_ingreso->format('d/m/Y') }}" readonly>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn btn-primary w-100">Editar Información</a>
            </div>
        </form>
    </div>
</div>


