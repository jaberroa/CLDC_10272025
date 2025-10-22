<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('dashboard') }}" class="d-flex align-items-end logo-main">
            <img height="35" width="34" class="logo-dark" alt="CLDCI Logo" src="{{ asset('assets/images/logo-md.png') }}">
            <img height="35" width="34" class="logo-light" alt="CLDCI Logo Light" src="{{ asset('assets/images/logo-md-light.png') }}">
            <h3 class="text-body-emphasis fw-bolder mb-0 ms-1">CLDCI</h3>
        </a>
        <button type="button" id="sidebarDefaultArrow" class="btn btn-sm p-0 fs-16 text-body-emphasis ms-auto float-end d-none icon-hover-btn d-none"><i class="ri-arrow-right-line fs-5"></i></button>
        <!--end::Brand Image-->
    </div>
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <div class="d-flex align-items-start flex-column w-100">
            <ul class="pe-main-menu list-unstyled">
                <!-- Main Menu -->
                <li class="pe-menu-title">Principal</li>
                
                <!-- Dashboard -->
                <li class="pe-slide">
                    <a href="{{ route('dashboard') }}" class="pe-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Dashboard</span>
                    </a>
                </li>

                <!-- Gestión de Miembros -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseMiembros" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('miembros.*') ? 'true' : 'false' }}" aria-controls="collapseMiembros">
                        <i class="ri-group-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Miembros</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse {{ request()->routeIs('miembros.*') ? 'show' : '' }}" id="collapseMiembros">
                        <li class="pe-slide-item">
                            <a href="{{ route('miembros.index') }}" class="pe-nav-link {{ request()->routeIs('miembros.index') ? 'active' : '' }}">
                                <i class="ri-list-check pe-nav-icon"></i>
                                <span class="pe-nav-content">Lista de Miembros</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('miembros.create') }}" class="pe-nav-link {{ request()->routeIs('miembros.create') ? 'active' : '' }}">
                                <i class="ri-user-add-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Agregar Miembro</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-id-card-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Carnets Digitales</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Gestión de Cuotas -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseCuotas" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('cuotas.*') ? 'true' : 'false' }}" aria-controls="collapseCuotas">
                        <i class="ri-money-dollar-circle-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Cuotas</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse {{ request()->routeIs('cuotas.*') ? 'show' : '' }}" id="collapseCuotas">
                        <li class="pe-slide-item">
                            <a href="{{ route('cuotas.index') }}" class="pe-nav-link {{ request()->routeIs('cuotas.index') ? 'active' : '' }}">
                                <i class="ri-list-check pe-nav-icon"></i>
                                <span class="pe-nav-content">Lista de Cuotas</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('cuotas.create') }}" class="pe-nav-link {{ request()->routeIs('cuotas.create') ? 'active' : '' }}">
                                <i class="ri-add-circle-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Nueva Cuota</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-bar-chart-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Reportes de Cuotas</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Directiva -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseDirectiva" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('directiva.*') ? 'true' : 'false' }}" aria-controls="collapseDirectiva">
                        <i class="ri-government-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Directiva</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse {{ request()->routeIs('directiva.*') ? 'show' : '' }}" id="collapseDirectiva">
                        <li class="pe-slide-item">
                            <a href="{{ route('directiva.index') }}" class="pe-nav-link {{ request()->routeIs('directiva.index') ? 'active' : '' }}">
                                <i class="ri-list-check pe-nav-icon"></i>
                                <span class="pe-nav-content">Períodos Directiva</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-star-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Miembros Directiva</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-calendar-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Cronograma</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Asambleas -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseAsambleas" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAsambleas">
                        <i class="ri-group-2-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Asambleas</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseAsambleas">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-calendar-event-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Próximas Asambleas</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-list-check-2 pe-nav-icon"></i>
                                <span class="pe-nav-content">Historial</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-check-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Asistencia</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Elecciones -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseElecciones" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseElecciones">
                        <i class="ri-vote-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Elecciones</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseElecciones">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-calendar-schedule-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Próximas Elecciones</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-star-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Candidatos</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-bar-chart-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Resultados</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Capacitaciones -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseCapacitaciones" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseCapacitaciones">
                        <i class="ri-graduation-cap-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Capacitaciones</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseCapacitaciones">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-calendar-event-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Próximos Cursos</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-book-open-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Catálogo</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-heart-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Mis Inscripciones</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Organizaciones -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseOrganizaciones" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseOrganizaciones">
                        <i class="ri-building-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Organizaciones</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseOrganizaciones">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-list-check pe-nav-icon"></i>
                                <span class="pe-nav-content">Lista de Organizaciones</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-add-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Registrar Nueva</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-file-list-3-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Documentación</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Finanzas -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseFinanzas" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseFinanzas">
                        <i class="ri-money-dollar-circle-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Finanzas</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseFinanzas">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-exchange-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Transacciones</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-file-chart-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Presupuestos</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-bar-chart-box-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Reportes</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Comunicaciones -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseComunicaciones" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseComunicaciones">
                        <i class="ri-message-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Comunicaciones</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseComunicaciones">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-megaphone-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Avisos</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-mail-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Notificaciones</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-file-text-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Documentos</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseReportes" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseReportes">
                        <i class="ri-bar-chart-2-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Reportes</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseReportes">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Miembros</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-building-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Organizaciones</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-money-dollar-circle-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Financieros</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Configuración -->
                <li class="pe-menu-title">Configuración</li>
                
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseConfiguracion" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseConfiguracion">
                        <i class="ri-settings-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Sistema</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseConfiguracion">
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-user-settings-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Usuarios</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-shield-user-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Roles y Permisos</span>
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="#" class="pe-nav-link">
                                <i class="ri-database-2-line pe-nav-icon"></i>
                                <span class="pe-nav-content">Base de Datos</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Perfil -->
                <li class="pe-slide">
                    <a href="#" class="pe-nav-link">
                        <i class="ri-user-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Mi Perfil</span>
                    </a>
                </li>

                <!-- Cerrar Sesión -->
                <li class="pe-slide">
                    <a href="{{ route('logout') }}" class="pe-nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ri-logout-box-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>

<!-- Formulario oculto para logout -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>