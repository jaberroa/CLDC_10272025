<!-- Simplebar Css -->
<link rel="stylesheet" href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}">
<!-- Swiper Css -->
<link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
<!-- Nouislider Css -->
<link href="{{ asset('assets/libs/nouislider/nouislider.min.css') }}" rel="stylesheet">
<!-- Bootstrap Css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
<!--icons css-->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

<!-- Prevenir flash del sidebar - CSS crítico -->
<style>
    /* Ocultar sidebar hasta que se carguen nuestros estilos */
    .pe-app-sidebar {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Mostrar sidebar cuando esté listo */
    .sidebar-loaded .pe-app-sidebar {
        opacity: 1;
    }
    
    /* Asegurar que nuestro sidebar tenga prioridad */
    .pe-app-sidebar {
        z-index: 1000;
    }
</style>

<!-- Estilos Globales CLDCI - CARGAR ANTES QUE APP.MIN.CSS -->
<link rel="stylesheet" href="{{ vite_asset('resources/css/global/app.css') }}">

<!-- App Css - CARGAR DESPUÉS DE NUESTROS ESTILOS -->
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">

<!-- Estilos específicos por módulo -->
@if(request()->routeIs('miembros.*'))
    <link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endif

<!-- Estilos específicos para cuotas -->
@if(request()->routeIs('cuotas.*'))
    <style>
        /* Aplicar sistema global completo a cuotas */
        .cuotas-table {
            @extend .table-global;
        }
        
        .cuotas-table .table {
            @extend .table;
        }
        
        .cuotas-table .table thead th {
            @extend .table thead th;
        }
        
        .cuotas-table .table tbody tr {
            @extend .table tbody tr;
        }
        
        .cuotas-table .table tbody td {
            @extend .table tbody td;
        }
        
        /* Aplicar estilos de formularios globales */
        .cuotas-form {
            @extend .form-global;
        }
        
        .cuotas-form .form-control,
        .cuotas-form .form-select {
            @extend .form-control;
        }
        
        .cuotas-form .form-label {
            @extend .form-label;
        }
        
        /* Aplicar estilos de filter panel globales */
        .cuotas-filter-panel {
            @extend .filter-panel-container;
        }
        
        .cuotas-filter-panel .btn-buscar {
            @extend .filter-panel-buttons .btn-buscar;
        }
        
        .cuotas-filter-panel .btn-limpiar {
            @extend .filter-panel-buttons .btn-limpiar;
        }
        
        /* Aplicar estilos de stat cards globales */
        .cuotas-stat-card {
            @extend .stat-card;
        }
        
        /* Aplicar estilos de badges globales */
        .cuotas-badge {
            @extend .badge;
        }
        
        /* Aplicar estilos de avatares globales */
        .cuotas-avatar {
            @extend .avatar-sm;
        }
        
        /* Aplicar estilos de botones globales */
        .cuotas-btn {
            @extend .btn;
        }
        
        /* Aplicar estilos de dropdown globales */
        .cuotas-dropdown {
            @extend .dropdown;
        }
        
        .cuotas-dropdown .dropdown-toggle {
            @extend .dropdown-toggle;
        }
        
        .cuotas-dropdown .dropdown-menu {
            @extend .dropdown-menu;
        }
        
        .cuotas-dropdown .dropdown-item {
            @extend .dropdown-item;
        }
        
        /* Aplicar estilos de paginación globales */
        .cuotas-pagination {
            @extend .pagination;
        }
        
        .cuotas-pagination .page-link {
            @extend .pagination .page-link;
        }
        
        /* Aplicar estilos de form check globales */
        .cuotas-form-check {
            @extend .form-check;
        }
        
        .cuotas-form-check .form-check-input {
            @extend .form-check-input;
        }
        
        .cuotas-form-check .form-check-label {
            @extend .form-check-label;
        }
        
        /* Aplicar estilos de sorting globales */
        .cuotas-sortable {
            @extend .sortable;
        }
        
        /* Aplicar estilos de progress scroll globales */
        .cuotas-progress-scroll {
            @extend .progress-scroll;
        }
        
        .cuotas-progress-scroll-bar {
            @extend .progress-scroll-bar;
        }
    </style>
@endif