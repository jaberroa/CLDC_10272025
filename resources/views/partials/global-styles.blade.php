{{-- ==========================================
   ESTILOS GLOBALES CLDCI - Helper para Vistas
   ========================================== --}}

{{-- Incluir estilos globales --}}
<link rel="stylesheet" href="{{ vite_asset('resources/css/global/app.css') }}">

{{-- Estilos específicos por módulo --}}
@if(request()->routeIs('miembros.*'))
    <link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endif

{{-- Estilos específicos por vista --}}
@if(request()->routeIs('cuotas.*'))
    <style>
        /* Aplicar estilos globales a vista cuotas */
        .card-body.table-body {
            padding: 1.5rem;
            background: #fff;
            border-radius: 0 0 0.5rem 0.5rem;
        }
        
        .card-body.table-body .table-responsive {
            border-radius: 0.375rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .card-body.table-body .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .card-body.table-body .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }
        
        .card-body.table-body .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .card-body.table-body .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .card-body.table-body .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }
        
        /* Aplicar estilos globales a formularios de cuotas */
        .form-global {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .form-global .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-global .form-control,
        .form-global .form-select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .form-global .form-control:focus,
        .form-global .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Aplicar estilos globales a filter panel de cuotas */
        .filter-panel-global {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .filter-panel-global .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1.25rem 1.5rem;
        }
        
        .filter-panel-global .card-body {
            padding: 1.5rem;
        }
        
        .filter-panel-global .form-control,
        .filter-panel-global .form-select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .filter-panel-global .form-control:focus,
        .filter-panel-global .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .filter-panel-global .btn-buscar,
        .filter-panel-global .btn-limpiar {
            padding: 0.625rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            min-width: 100px;
            height: 38px;
        }
        
        .filter-panel-global .btn-buscar {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
        }
        
        .filter-panel-global .btn-buscar:hover {
            background: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .filter-panel-global .btn-limpiar {
            background: #fff;
            border-color: #d1d5db;
            color: #6b7280;
        }
        
        .filter-panel-global .btn-limpiar:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            color: #374151;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endif


