<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title>@yield('title', ' | Urbix Admin & Dashboards Template')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Admin & Dashboards Template" name="description" />
<meta content="Pixeleyez" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- layout setup -->
<script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

@yield('css')
@include('partials.head-css')

<body>
<div id="layout-wrapper">
    @include('partials.header')
    @include('partials.sidebar')
    @include('partials.horizontal')

    <main class="app-wrapper">
        <div class="container-fluid">

            @include('partials.page-title')
        @yield('content')

        @include('partials.switcher')
        @include('partials.scroll-to-top')

        </div>
    </main>
    
    @include('partials.footer')

    {{-- Toast Global --}}
    @include('components.global-toast')
    @include('components.modals.delete-confirmation')

    @include('partials.vendor-scripts')

    {{-- Toast Global JavaScript --}}
    <script src="{{ vite_asset('resources/js/toast-simple.js') }}"></script>

    {{-- Prevenir flash del sidebar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clase para mostrar sidebar cuando esté listo
            document.body.classList.add('sidebar-loaded');
        });
    </script>

    @yield('js')

</body>

</html>
