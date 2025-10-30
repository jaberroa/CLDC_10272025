@props(['tipo'])

@php
    $badges = [
        'nacional' => ['class' => 'bg-primary', 'text' => 'Nacional'],
        'seccional' => ['class' => 'bg-info', 'text' => 'Seccional'],
        'seccional_internacional' => ['class' => 'bg-warning', 'text' => 'Internacional'],
        'diaspora' => ['class' => 'bg-secondary', 'text' => 'Diáspora'],
    ];
    
    $badge = $badges[$tipo] ?? ['class' => 'bg-secondary', 'text' => ucfirst($tipo)];
@endphp

<span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>

