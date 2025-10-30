@extends('partials.layouts.master')

@section('title', 'Reportes de Cuotas | CLDCI')
@section('title-sub', 'Finanzas')
@section('pagetitle', 'Reportes de Cuotas')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/cuotas-show-header.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('cuotas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title"><i class="ri-bar-chart-2-line"></i> Reportes de Cuotas</h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: .9rem; opacity:.8">Análisis y métricas financieras de cuotas</p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3 gap-2">
                    <button class="btn btn-outline-light btn-sm" onclick="window.print()"><i class="ri-printer-line me-1"></i> Imprimir</button>
                    <a class="btn btn-outline-light btn-sm" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="ri-download-2-line me-1"></i> Exportar CSV</a>
                </div>
            </div>
            <div class="card-body table-body">
                <!-- KPIs -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Total Cuotas" :value="number_format($kpis['total'])" icon="ri-file-list-3-line" background="bg-primary-subtle" icon-background="bg-primary" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Pendientes" :value="number_format($kpis['pendientes'])" icon="ri-time-line" background="bg-warning-subtle" icon-background="bg-warning" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Pagadas" :value="number_format($kpis['pagadas'])" icon="ri-check-line" background="bg-success-subtle" icon-background="bg-success" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Vencidas" :value="number_format($kpis['vencidas'])" icon="ri-error-warning-line" background="bg-danger-subtle" icon-background="bg-danger" />
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Monto Total" :value="'RD$ '.number_format($kpis['monto_total'], 2)" icon="ri-money-dollar-circle-line" background="bg-info-subtle" icon-background="bg-info" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Pendiente" :value="'RD$ '.number_format($kpis['monto_pendiente'], 2)" icon="ri-money-dollar-box-line" background="bg-warning-subtle" icon-background="bg-warning" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <x-miembros.stat-card title="Pagado" :value="'RD$ '.number_format($kpis['monto_pagado'], 2)" icon="ri-bank-card-line" background="bg-success-subtle" icon-background="bg-success" />
                    </div>
                </div>

                <!-- Filtros (consistentes con Miembros) -->
                <x-global-filter
                    title="Filtros de Búsqueda"
                    description="Refine los resultados utilizando los filtros disponibles"
                    icon="ri-search-line"
                    form-id="cuotas-reportes-filtros"
                    form-action="{{ route('cuotas.reportes') }}"
                    clear-url="{{ route('cuotas.reportes') }}"
                    submit-label="Buscar"
                    clear-label="Limpiar"
                    variant="default"
                    :filters="[
                        [
                            'name' => 'estado',
                            'label' => 'Estado',
                            'type' => 'select',
                            'placeholder' => 'Todos',
                            'col' => 'col-md-3',
                            'options' => ['pendiente' => 'Pendiente', 'pagada' => 'Pagada', 'vencida' => 'Vencida', 'cancelada' => 'Cancelada']
                        ],
                        [
                            'name' => 'tipo_cuota',
                            'label' => 'Tipo',
                            'type' => 'select',
                            'placeholder' => 'Todos',
                            'col' => 'col-md-3',
                            'options' => ['mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'anual' => 'Anual']
                        ],
                        [
                            'name' => 'miembro_id',
                            'label' => 'Miembro',
                            'type' => 'select',
                            'placeholder' => 'Todos',
                            'col' => 'col-md-3',
                            'options' => $miembros->pluck('nombre_completo','id')->toArray()
                        ],
                        [
                            'name' => 'fecha_desde',
                            'label' => 'Desde',
                            'type' => 'date',
                            'col' => 'col-md-3'
                        ],
                        [
                            'name' => 'fecha_hasta',
                            'label' => 'Hasta',
                            'type' => 'date',
                            'col' => 'col-md-3'
                        ]
                    ]"
                />

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Miembro</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="text-end">Monto</th>
                                <th>Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuotas as $c)
                            <tr>
                                <td>{{ $c->miembro->nombre_completo }}</td>
                                <td>{{ ucfirst($c->tipo_cuota) }}</td>
                                <td>{{ ucfirst($c->estado) }}</td>
                                <td class="text-end">RD$ {{ number_format($c->monto, 2) }}</td>
                                <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">Sin resultados</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $cuotas->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


