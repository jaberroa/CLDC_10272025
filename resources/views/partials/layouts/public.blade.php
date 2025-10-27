<!doctype html>
<html lang="es" data-layout="vertical" data-sidebar="light">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'CLDCI')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    @yield('css')

    <style>
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        
        .public-footer {
            margin-top: 3rem;
            padding: 2rem 0;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <!-- Public Header -->
    <div class="public-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ri-shield-check-line fs-2 me-3"></i>
                    <div>
                        <h4 class="mb-0">CLDCI</h4>
                        <small>Sistema de Votación Segura</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark">
                        <i class="ri-lock-line me-1"></i>
                        Conexión Segura
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Public Footer -->
    <div class="public-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">
                        <i class="ri-shield-check-line me-1"></i>
                        <strong>CLDCI</strong> - Sistema de Votación Segura
                    </p>
                    <small class="text-muted">Votación anónima con encriptación JWT</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        © {{ date('Y') }} CLDCI. Todos los derechos reservados.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Toasts will be added here by JavaScript -->
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>

    <!-- Toast Functions -->
    <script>
        function showSuccessToast(message) {
            showToast(message, 'success');
        }

        function showErrorToast(message) {
            showToast(message, 'danger');
        }

        function showInfoToast(message) {
            showToast(message, 'info');
        }

        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();
            
            const iconMap = {
                'success': 'ri-check-line',
                'danger': 'ri-error-warning-line',
                'info': 'ri-information-line',
                'warning': 'ri-alert-line'
            };
            
            const icon = iconMap[type] || iconMap['info'];
            
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="${icon} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
    </script>

    @yield('js')
</body>

</html>


