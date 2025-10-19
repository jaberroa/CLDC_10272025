<!DOCTYPE html>
<html lang="es">

<meta charset="utf-8" />
<title>@yield('title', ' | CLDCI - Sistema de Gestión')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Sistema de Gestión CLDCI" name="description" />
<meta content="CLDCI" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- layout setup -->
<script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

@yield('css')
@include('partials.head-css')
<!-- Toast Animations CSS -->
<link href="{{ asset('assets/css/toast-animations.css') }}" rel="stylesheet" type="text/css">

<body>
    
@include('partials.auth-background')
@include('partials.auth-header')
        @yield('content')

        @include('partials.vendor-scripts')

        @yield('js')

</body>

</html>
