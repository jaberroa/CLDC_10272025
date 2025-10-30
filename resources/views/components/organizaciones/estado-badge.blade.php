@props(['estado'])

@php
    $badges = [
        'activa' => ['class' => 'bg-success', 'text' => 'Activa'],
        'inactiva' => ['class' => 'bg-secondary', 'text' => 'Inactiva'],
        'suspendida' => ['class' => 'bg-danger', 'text' => 'Suspendida'],
    ];
    
    $badge = $badges[$estado] ?? ['class' => 'bg-secondary', 'text' => ucfirst($estado)];
@endphp

<span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>

