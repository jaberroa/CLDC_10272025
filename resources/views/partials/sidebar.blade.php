<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('dashboard') }}" class="d-flex align-items-end logo-main">
            <img height="35" width="34" class="logo-dark" alt="CLDCI Logo" src="{{ asset('assets/images/logo-md.png') }}">
            <img height="35" width="34" class="logo-light" alt="CLDCI Logo" src="{{ asset('assets/images/logo-md-light.png') }}">
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
                        <i class="ri-dashboard-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Dashboard</span>
                    </a>
                </li>
                
                <!-- Gestión de Miembros -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseMiembros" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseMiembros">
                        <i class="ri-group-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Miembros</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseMiembros">
                        <li class="pe-slide-item">
                            <a href="{{ route('miembros.index') }}" class="pe-nav-link">
                                Lista de Miembros
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('miembros.create') }}" class="pe-nav-link">
                                Nuevo Miembro
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('miembros.index') }}" class="pe-nav-link">
                                Carnet Digital
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Directiva -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseDirectiva" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseDirectiva">
                        <i class="ri-government-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Directiva</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseDirectiva">
                        <li class="pe-slide-item">
                            <a href="{{ route('directiva.index') }}" class="pe-nav-link">
                                Organigrama
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('directiva.cargos') }}" class="pe-nav-link">
                                Cargos Directivos
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('directiva.mandatos') }}" class="pe-nav-link">
                                Historial de Mandatos
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Asambleas -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseAsambleas" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAsambleas">
                        <i class="ri-calendar-event-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Asambleas</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseAsambleas">
                        <li class="pe-slide-item">
                            <a href="{{ route('asambleas.index') }}" class="pe-nav-link">
                                Lista de Asambleas
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('asambleas.create') }}" class="pe-nav-link">
                                Nueva Asamblea
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('asambleas.asistencia') }}" class="pe-nav-link">
                                Control de Asistencia
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Elecciones -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseElecciones" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseElecciones">
                        <i class="ri-government-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Elecciones</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseElecciones">
                        <li class="pe-slide-item">
                            <a href="{{ route('elecciones.index') }}" class="pe-nav-link">
                                Procesos Electorales
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('elecciones.candidatos') }}" class="pe-nav-link">
                                Candidatos
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('elecciones.votacion') }}" class="pe-nav-link">
                                Votación Digital
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Formación -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseFormacion" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseFormacion">
                        <i class="ri-graduation-cap-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Formación</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseFormacion">
                        <li class="pe-slide-item">
                            <a href="{{ route('cursos.index') }}" class="pe-nav-link">
                                Cursos y Talleres
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('cursos.inscripciones') }}" class="pe-nav-link">
                                Inscripciones
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('cursos.certificados') }}" class="pe-nav-link">
                                Certificados
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Reportes -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseReportes" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseReportes">
                        <i class="ri-bar-chart-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Reportes</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseReportes">
                        <li class="pe-slide-item">
                            <a href="{{ route('reportes.miembros') }}" class="pe-nav-link">
                                Reporte de Miembros
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('reportes.financiero') }}" class="pe-nav-link">
                                Reporte Financiero
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('reportes.actividades') }}" class="pe-nav-link">
                                Actividades
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Transparencia -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseTransparencia" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseTransparencia">
                        <i class="ri-file-text-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Transparencia</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseTransparencia">
                        <li class="pe-slide-item">
                            <a href="{{ route('documentos.index') }}" class="pe-nav-link">
                                Documentos Legales
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('documentos.actas') }}" class="pe-nav-link">
                                Actas de Asambleas
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('documentos.estatutos') }}" class="pe-nav-link">
                                Estatutos y Reglamentos
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Configuración -->
                <li class="pe-menu-title">Configuración</li>
                
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseConfiguracion" class="pe-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseConfiguracion">
                        <i class="ri-settings-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Configuración</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>
                    <ul class="pe-slide-menu collapse" id="collapseConfiguracion">
                        <li class="pe-slide-item">
                            <a href="{{ route('organizaciones.index') }}" class="pe-nav-link">
                                Organizaciones
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('usuarios.index') }}" class="pe-nav-link">
                                Usuarios del Sistema
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="{{ route('configuracion.general') }}" class="pe-nav-link">
                                Configuración General
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Perfil -->
                <li class="pe-slide">
                    <a href="{{ route('profile.edit') }}" class="pe-nav-link">
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

<!-- Formulario de logout oculto -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>