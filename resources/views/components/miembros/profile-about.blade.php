@props(['miembro'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Acerca de {{ $miembro->nombre }}</h5>
        <span class="badge bg-success-subtle text-success">Miembro Activo</span>
    </div>
    <div class="card-body">
        <p>
            Como {{ $miembro->tipo_membresia }} de {{ $miembro->organizacion->nombre }}, {{ $miembro->nombre }} ha demostrado un compromiso constante con los valores y objetivos de la organización. Su participación activa en asambleas, capacitaciones y procesos democráticos refleja su dedicación al desarrollo institucional.
        </p>
        <p class="mb-0">
            Con {{ $miembro->fecha_ingreso->diffInYears(now()) }} años de experiencia como miembro, ha contribuido significativamente al crecimiento y fortalecimiento de la organización. Su profesionalismo y compromiso son un ejemplo para otros miembros, demostrando que la participación activa es fundamental para el éxito colectivo.
        </p>
    </div>
</div>


