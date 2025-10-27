<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación: {{ $eleccion->titulo }} | CLDCI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .voting-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .voting-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            margin: 0 1rem;
        }
        
        .voting-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .voting-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .voting-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .countdown-timer {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        .countdown-timer h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .countdown-display {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }
        
        .voting-body {
            padding: 2rem;
        }
        
        .candidate-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .candidate-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }
        
        .candidate-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .candidate-card.selected::before {
            content: '✓';
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #28a745;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .candidate-info h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .candidate-info p {
            color: #666;
            margin-bottom: 0;
        }
        
        .voter-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .voter-form h5 {
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-votar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-votar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .btn-votar:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .voting-header h1 {
                font-size: 1.5rem;
            }
            
            .voting-header p {
                font-size: 1rem;
            }
            
            .voting-body {
                padding: 1rem;
            }
            
            .candidate-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="voting-container">
        <div class="voting-card">
            <!-- Header -->
            <div class="voting-header">
                <h1><i class="ri-vote-line me-2"></i>{{ $eleccion->titulo }}</h1>
                <p>{{ $eleccion->organizacion->nombre ?? 'CLDCI' }}</p>
                
                @if($eleccion->descripcion)
                    <p class="mt-2">{{ $eleccion->descripcion }}</p>
                @endif
                
                <!-- Countdown Timer -->
                <div class="countdown-timer">
                    <h5><i class="ri-time-line me-1"></i>Tiempo restante</h5>
                    <div class="countdown-display" id="countdown">
                        <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="voting-body">
                <!-- Candidatos -->
                <h4 class="mb-3"><i class="ri-user-line me-2"></i>Selecciona tu candidato:</h4>
                
                <form id="votingForm">
                    @csrf
                    <input type="hidden" name="candidato_id" id="candidato_id">
                    
                    @foreach($candidatos as $candidato)
                        <div class="candidate-card" data-candidato-id="{{ $candidato->id }}">
                            <div class="candidate-info">
                                <h5>{{ $candidato->miembro->nombre_completo ?? 'Candidato ' . $candidato->id }}</h5>
                                @if($candidato->cargo)
                                    <p><i class="ri-briefcase-line me-1"></i>{{ $candidato->cargo->nombre ?? 'Cargo no especificado' }}</p>
                                @endif
                                @if($candidato->propuesta)
                                    <p><i class="ri-file-text-line me-1"></i>{{ Str::limit($candidato->propuesta, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Formulario del votante -->
                    <div class="voter-form">
                        <h5><i class="ri-user-line me-2"></i>Información del votante</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_votante" class="form-label">Nombre completo *</label>
                                <input type="text" class="form-control" id="nombre_votante" name="nombre_votante" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cedula_votante" class="form-label">Cédula *</label>
                                <input type="text" class="form-control" id="cedula_votante" name="cedula_votante" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_votante" class="form-label">Email (opcional)</label>
                            <input type="email" class="form-control" id="email_votante" name="email_votante">
                        </div>
                        
                        <button type="submit" class="btn btn-votar" id="btnVotar" disabled>
                            <i class="ri-checkbox-circle-line me-2"></i>Votar
                        </button>
                    </div>
                </form>
                
                <!-- Loading -->
                <div class="loading text-center mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Procesando voto...</span>
                    </div>
                    <p class="mt-2">Procesando tu voto...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let selectedCandidate = null;
        let countdownInterval = null;
        
        // Configurar CSRF token para AJAX
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Selección de candidatos
        document.querySelectorAll('.candidate-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remover selección anterior
                document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
                
                // Seleccionar nuevo candidato
                this.classList.add('selected');
                selectedCandidate = this.dataset.candidatoId;
                document.getElementById('candidato_id').value = selectedCandidate;
                
                // Habilitar botón de votar
                document.getElementById('btnVotar').disabled = false;
            });
        });
        
        // Countdown timer
        function startCountdown() {
            const endTime = new Date('{{ $fechaFin->toISOString() }}').getTime();
            
            countdownInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = endTime - now;
                
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById('countdown').innerHTML = "00:00:00";
                    // Redirigir o mostrar mensaje de que la votación terminó
                    Swal.fire({
                        title: 'Votación terminada',
                        text: 'El tiempo de votación ha expirado.',
                        icon: 'info',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }
                
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            }, 1000);
        }
        
        // Envío del formulario
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedCandidate) {
                Swal.fire({
                    title: 'Selecciona un candidato',
                    text: 'Por favor, selecciona un candidato antes de votar.',
                    icon: 'warning'
                });
                return;
            }
            
            Swal.fire({
                title: '¿Confirmar voto?',
                text: '¿Estás seguro de que deseas votar por este candidato? Esta acción no se puede deshacer.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, votar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitVote();
                }
            });
        });
        
        function submitVote() {
            const formData = new FormData(document.getElementById('votingForm'));
            
            // Mostrar loading
            document.querySelector('.loading').classList.add('show');
            document.getElementById('btnVotar').disabled = true;
            
            fetch('{{ route("votacion.publica.submit", $eleccion->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('.loading').classList.remove('show');
                
                if (data.success) {
                    Swal.fire({
                        title: '¡Voto registrado!',
                        text: data.mensaje,
                        icon: 'success',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        // Redirigir o cerrar la ventana
                        window.close();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje,
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    document.getElementById('btnVotar').disabled = false;
                }
            })
            .catch(error => {
                document.querySelector('.loading').classList.remove('show');
                document.getElementById('btnVotar').disabled = false;
                
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al procesar tu voto. Por favor, inténtalo de nuevo.',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            });
        }
        
        // Inicializar countdown
        startCountdown();
    </script>
</body>
</html>
