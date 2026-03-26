@props(['estado' => 'activo'])

@php
    [$bgClass, $dotColor, $textColor, $label] = match(strtolower($estado)) {
        'activo'   => ['ds-badge--activo',   '#12B76A', '#027A48', 'Activo'],
        'inactivo' => ['ds-badge--inactivo', '#F79009', '#B54708', 'Inactivo'],
        'cerrado'  => ['ds-badge--cerrado',  '#98A2B3', '#344054', 'Cerrado'],
        default    => ['ds-badge--cerrado',  '#98A2B3', '#344054', ucfirst($estado)],
    };
@endphp

<span class="ds-badge {{ $bgClass }}" data-estado="{{ strtolower($estado) }}">
    <span class="ds-badge-dot" style="background: {{ $dotColor }};"></span>
    {{ $label }}
</span>
