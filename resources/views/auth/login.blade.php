<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Isprotik</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .network-background {
            background-image: url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(0,119,182,0.9) 0%, rgba(0,180,216,0.8) 100%);
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-card {
        background-color: rgba(255, 255, 255, 0.3); /* Cambiado de 0.1 a 0.3 (más opaco) */
        backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.3); /* Ajustado para coincidir */
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 450px;
        padding: 2.5rem;
        color: white;
        margin: auto;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            height: 20px; /* Altura reducida */
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }
        .input-group-text {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            height: 40px; /* Altura reducida */
            padding: 0 15px;
        }
        .btn-custom {
            background-color: white;
            color: #0077B6;
            border: none;
            border-radius: 8px;
            transition: all 0.3s;
            padding: 10px 0;
            font-weight: 600;
            height: 40px; /* Altura reducida */
            margin-top: 1rem; /* Espacio superior aumentado */
        }
        .btn-custom:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-img {
            height: 150px;
            width: auto;
            /* margin-bottom: 0.5rem; */
        }
        .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .alert {
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.7);
        }
        .alert-success {
            background-color: rgba(25, 135, 84, 0.7);
        }
        a {
            color: white;
            text-decoration: underline;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        a:hover {
            color: rgba(255, 255, 255, 0.8);
        }
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 1.1em;
            height: 1.1em;
            margin-top: 0.15em;
        }
        .form-check-input:checked {
            background-color: #0077B6;
            border-color: #0077B6;
        }
        .form-check-label {
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="network-background">
        <div class="gradient-overlay">
            <div class="login-card">
                <!-- Logo -->
                <div class="logo-container">
                    <img src="{{ asset('img/logo.png') }}" alt="Isprotik Logo" class="logo-img">
                    {{-- <h1 class="fs-3 fw-bold text-white">Isprotik</h1> --}}
                    <p class="text-muted mt-1">Administrador de ISP</p>
                </div>

                <!-- Errores de validación -->
                <x-validation-errors class="alert alert-danger" />

                <!-- Mensaje de estado -->
                @session('status')
                    <div class="alert alert-success">
                        {{ $value }}
                    </div>
                @endsession

                <!-- Formulario -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label text-white mb-1">{{ __('Correo Electrónico') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope-fill text-white"></i>
                            </span>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@email.com">
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password" class="form-label text-white mb-1">{{ __('Contraseña') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill text-white"></i>
                            </span>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Recordar sesión y Olvidé contraseña -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label text-white" for="remember_me">{{ __('Recordar sesión') }}</label>
                        </div>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Botón de Ingreso -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Ingresar') }}
                        </button>
                    </div>

                    <!-- Enlace a registro -->
                    @if (Route::has('register'))
                        <div class="text-center mt-3">
                            <p class="text-muted">¿No tienes una cuenta? <a href="{{ route('register') }}" class="fw-semibold">Regístrate</a></p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>