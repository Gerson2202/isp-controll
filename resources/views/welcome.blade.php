<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Isprotik - Administrador de ISP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom Styles -->
        <style>
            body {
                font-family: 'Figtree', sans-serif;
            }
            .network-background {
                background-image: url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                min-height: 100vh;
            }
            .gradient-overlay {
                background: linear-gradient(135deg, rgba(0,119,182,0.9) 0%, rgba(0,180,216,0.8) 100%);
                min-height: 100vh;
            }
            .feature-card {
                transition: all 0.3s ease;
                backdrop-filter: blur(5px);
                background-color: rgba(255, 255, 255, 0.1);
                height: 100%;
            }
            .feature-card:hover {
                transform: translateY(-5px);
                background-color: rgba(255, 255, 255, 0.2);
            }
            .testimonial-card {
                backdrop-filter: blur(5px);
                background-color: rgba(255, 255, 255, 0.15);
                border-left: 4px solid white;
                height: 100%;
            }
            .stats-card {
                backdrop-filter: blur(5px);
                background-color: rgba(255, 255, 255, 0.1);
                transition: all 0.3s ease;
            }
            .stats-card:hover {
                background-color: rgba(255, 255, 255, 0.2);
                transform: scale(1.05);
            }
            .cta-section {
                background: linear-gradient(135deg, rgba(0,119,182,0.95) 0%, rgba(0,180,216,0.9) 100%);
                backdrop-filter: blur(5px);
                border-top: 1px solid rgba(255,255,255,0.2);
                border-bottom: 1px solid rgba(255,255,255,0.2);
            }
            .logo-img {
                max-height: 80px;
                width: auto;
            }
            .nav-link-custom {
                border-radius: 0.375rem;
                padding: 0.5rem 1rem;
            }
            .btn-login {
                border: 1px solid white;
                color: white;
            }
            .btn-login:hover {
                background-color: white;
                color: #0077b6;
            }
            .btn-register {
                background-color: white;
                color: #0077b6;
            }
            .btn-register:hover {
                background-color: #e9f5ff;
            }
            .text-blue-100 {
                color: rgba(255, 255, 255, 0.8);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="network-background">
            <div class="gradient-overlay d-flex flex-column">
                <div class="container py-5 px-3 px-md-5">
                    <!-- Header -->
                    <header class="d-flex flex-column flex-md-row align-items-center justify-content-between py-5">
                        <!-- Logo and Name -->
                        <div class="d-flex align-items-center mb-4 mb-md-0">
                            <img src="{{ asset('img/logo.png') }}" alt="Isprotik Logo" class="logo-img me-3">
                            <span class="fs-5 fw-bold text-white">Isprotik</span>
                        </div>
                        
                        <!-- Title -->
                        <h1 class="text-center mb-4 mb-md-0 mx-md-auto order-md-1 fs-3 fw-bold text-white">
                            Administrador de ISP
                        </h1>
                        
                        <!-- Navigation -->
                        @if (Route::has('login'))
                            <nav class="d-flex ms-md-auto order-md-2">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-login">
                                        Panel de Control
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-login me-2">
                                        Iniciar Sesión
                                    </a>
{{-- 
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-register">
                                            Registrarse
                                        </a>
                                    @endif --}}
                                @endauth
                            </nav>
                        @endif
                    </header>

                    <!-- Main Content -->
                    <main class="mt-5">
                        <!-- Hero Section -->
                        <section class="text-center mb-5">
                            <h2 class="display-4 fw-bold text-white mb-4">Gestión Integral para Proveedores de Internet</h2>
                            <p class="fs-4 text-blue-100 mx-auto" style="max-width: 800px;">
                                La solución todo en uno para administrar clientes, facturación, tickets y monitoreo de red en MikroTik.
                            </p>
                        </section>

                        <!-- Features Section -->
                        <section class="mb-5">
                            <div class="row g-4">
                                <!-- Feature 1 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Gestión de Clientes</h3>
                                        <p class="text-blue-100">
                                            Administra toda la información de tus clientes de forma organizada y accesible desde un solo lugar.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Feature 2 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Facturación Simple</h3>
                                        <p class="text-blue-100">
                                            Genera facturas automáticas, gestiona pagos y mantén un historial completo de transacciones.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Feature 3 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Monitoreo de Nodos</h3>
                                        <p class="text-blue-100">
                                            Supervisa el estado de tu red en tiempo real y recibe alertas ante cualquier incidencia.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Feature 4 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Sistema de Tickets</h3>
                                        <p class="text-blue-100">
                                            Atiende las solicitudes de soporte de tus clientes de manera organizada y eficiente.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Feature 5 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Planes y Servicios</h3>
                                        <p class="text-blue-100">
                                            Configura y gestiona diferentes planes de servicio con sus características y precios.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Feature 6 -->
                                <div class="col-md-4">
                                    <div class="feature-card rounded p-4 p-lg-5 text-white border border-white border-opacity-20">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-10 mb-4" style="width: 48px; height: 48px;">
                                            <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="fs-5 fw-semibold mb-3">Integración MikroTik</h3>
                                        <p class="text-blue-100">
                                            Conexión directa con tus routers MikroTik para gestión automatizada de clientes y servicios.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Testimonials Section -->
                        <section class="my-5 py-5">
                            <div class="text-center mb-5">
                                <h2 class="display-5 fw-bold text-white mb-3">Lo que dicen nuestros clientes</h2>
                                <p class="fs-5 text-blue-100 mx-auto" style="max-width: 800px;">ISP de todo el país confían en Isprotik para gestionar sus redes</p>
                            </div>

                            <div class="row g-4 mb-5">
                                <!-- Testimonial 1 -->
                                <div class="col-md-6">
                                    <div class="testimonial-card rounded p-4 p-lg-5 text-white h-100">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="fw-semibold mb-0">Carlos Méndez</h4>
                                                <p class="text-blue-100 small">ISP Conexión Total, Medellín</p>
                                            </div>
                                        </div>
                                        <p class="fst-italic">"Isprotik ha revolucionado nuestra gestión de clientes. Redujimos el tiempo de facturación en un 70% y nuestros clientes están más satisfechos con el sistema de tickets."</p>
                                    </div>
                                </div>
                                
                                <!-- Testimonial 2 -->
                                <div class="col-md-6">
                                    <div class="testimonial-card rounded p-4 p-lg-5 text-white h-100">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="fw-semibold mb-0">María Fernanda Gómez</h4>
                                                <p class="text-blue-100 small">Redes Avanzadas, Bogotá</p>
                                            </div>
                                        </div>
                                        <p class="fst-italic">"La integración con MikroTik es impecable. Ahora podemos gestionar cortes masivos y cambios de planes en minutos, no en horas. Nuestra productividad se ha disparado."</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="row g-4 mb-5 text-center">
                                <div class="col-md-4">
                                    <div class="stats-card rounded p-4 text-white">
                                        <div class="display-4 fw-bold mb-2">+85%</div>
                                        <p class="text-blue-100">Reducción en tiempo de facturación</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card rounded p-4 text-white">
                                        <div class="display-4 fw-bold mb-2">+50</div>
                                        <p class="text-blue-100">ISPS satisfechos en Colombia & Venezuela</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card rounded p-4 text-white">
                                        <div class="display-4 fw-bold mb-2">24/7</div>
                                        <p class="text-blue-100">Soporte técnico especializado</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- CTA Section -->
                        <section class="cta-section rounded-3 p-4 p-md-5 my-5 text-center">
                            <h2 class="display-5 fw-bold text-white mb-4">¿Listo para transformar la gestión de tu ISP?</h2>
                            <p class="fs-4 text-blue-100 mb-4 mx-auto" style="max-width: 800px;">Únete a cientos de proveedores que ya optimizaron sus operaciones con Isprotik</p>
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                <a href="#" class="btn btn-white text-primary fw-semibold py-3 px-5 rounded-3">
                                    Prueba Gratis por 14 Días
                                </a>
                                <a href="https://wa.me/573215852059" target="_blank" class="btn btn-outline-white text-white fw-semibold py-3 px-5 rounded-3">
                                    Hablar con un Asesor
                                </a>
                            </div>
                        </section>

                        <!-- Logo -->
                        <div class="d-flex justify-content-center my-5">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo Isprotik" class="img-fluid" style="height: 160px; width: auto;">
                        </div>
                    </main>

                    <!-- Footer -->
                    <footer class="py-5 text-center text-white text-opacity-80 small">
                        Isprotik &copy; {{ date('Y') }} - Sistema de Gestión para Proveedores de Internet | 
                        Contáctame: <a href="tel:+573215852059" class="text-decoration-none text-white text-opacity-80 hover:text-white">+57 321 5852059</a>
                    </footer>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>