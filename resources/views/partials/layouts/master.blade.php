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

    {{-- SweetAlert2 (para fallback de toasts) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Toast Global JavaScript --}}
    <script src="{{ asset('assets/js/toast-simple.js') }}" type="text/javascript"></script>

    {{-- Disparadores globales de toast basados en la sesión --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
    <script>
        // Cargar SweetAlert2 como respaldo si no existen las funciones globales
        if (typeof window.Swal === 'undefined') {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(s);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const show = (type, msg) => {
                const doShow = () => {
                    if (typeof window.showToast === 'function') {
                        const map = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
                        window.showToast(msg, map[type] || 'success');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({ toast: true, position: 'top-end', icon: type, title: msg, showConfirmButton: false, timer: 3000, timerProgressBar: true });
                    } else {
                        setTimeout(doShow, 100); // reintentar hasta que cargue
                    }
                };
                doShow();
            };

            @if(session('success'))
                show('success', @json(session('success')));
            @endif
            @if(session('error'))
                show('error', @json(session('error')));
            @endif
            @if(session('warning'))
                show('warning', @json(session('warning')));
            @endif
            @if(session('info'))
                show('info', @json(session('info')));
            @endif
        });
    </script>
    @endif

    {{-- Prevenir flash del sidebar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clase para mostrar sidebar cuando esté listo
            document.body.classList.add('sidebar-loaded');
        });
    </script>

    {{-- Bootstrap 5 JS para funcionalidad completa de modales --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    @yield('js')

</body>

</html>
