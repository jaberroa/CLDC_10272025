@props([
    'title' => 'Filtros de Búsqueda',
    'description' => 'Refine los resultados utilizando los filtros disponibles',
    'icon' => 'ri-search-line',
    'formId' => 'global-filters-form',
    'formAction' => '',
    'filters' => [],
    'submitLabel' => 'Buscar',
    'clearLabel' => 'Limpiar',
    'clearUrl' => '',
    'showButtons' => true,
    'variant' => 'default' // default, compact, expanded
])

<div class="col-xxl-12">
    <div class="card shadow-sm global-filter-container">
        <div class="card-header global-filter-header">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="global-filter-icon">
                        <i class="{{ $icon }}"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="global-filter-title">{{ $title }}</h5>
                    <p class="global-filter-description">{{ $description }}</p>
                </div>
            </div>
        </div>
        <div class="card-body global-filter-body">
            <form method="GET" action="{{ $formAction }}" id="{{ $formId }}" class="global-filter-form">
                <div class="global-filter-grid">
                    @foreach($filters as $filter)
                        <div class="global-filter-field {{ $filter['col'] ?? 'col-md-3' }}">
                            <label for="{{ $filter['name'] }}" class="global-filter-label">{{ $filter['label'] }}</label>
                            
                            @if($filter['type'] === 'text')
                                <input type="text" 
                                       class="global-filter-input" 
                                       id="{{ $filter['name'] }}" 
                                       name="{{ $filter['name'] }}" 
                                       value="{{ request($filter['name']) }}" 
                                       placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @elseif($filter['type'] === 'select')
                                <select class="global-filter-select" id="{{ $filter['name'] }}" name="{{ $filter['name'] }}">
                                    <option value="">{{ $filter['placeholder'] ?? 'Todos' }}</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($filter['type'] === 'date')
                                <input type="date" 
                                       class="global-filter-input" 
                                       id="{{ $filter['name'] }}" 
                                       name="{{ $filter['name'] }}" 
                                       value="{{ request($filter['name']) }}">
                            @endif
                        </div>
                    @endforeach
                    
                    @if($showButtons)
                        <div class="global-filter-actions">
                            <button type="submit" class="global-filter-btn global-filter-btn-primary">
                                <i class="ri-search-line"></i>
                                <span>{{ $submitLabel }}</span>
                            </button>
                            <a href="{{ $clearUrl }}" class="global-filter-btn global-filter-btn-secondary">
                                <i class="ri-refresh-line"></i>
                                <span>{{ $clearLabel }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>


