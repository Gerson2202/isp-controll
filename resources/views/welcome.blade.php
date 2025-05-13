<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Isprotik - Administrador de ISP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com */
                /* [Estilos Tailwind originales aquí...] */
            </style>
        @endif
        <style>
            .network-background {
                background-image: url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
            .gradient-overlay {
                background: linear-gradient(135deg, rgba(0,119,182,0.9) 0%, rgba(0,180,216,0.8) 100%);
            }
            .feature-card {
                transition: all 0.3s ease;
                backdrop-filter: blur(5px);
                background-color: rgba(255, 255, 255, 0.1);
            }
            .feature-card:hover {
                transform: translateY(-5px);
                background-color: rgba(255, 255, 255, 0.2);
            }
            .testimonial-card {
                backdrop-filter: blur(5px);
                background-color: rgba(255, 255, 255, 0.15);
                border-left: 4px solid white;
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
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="network-background min-h-screen">
            <div class="gradient-overlay min-h-screen">
                <div class="relative min-h-screen flex flex-col items-center justify-center">
                    <div class="relative w-full max-w-6xl px-6 py-10">
                        <header class="grid grid-cols-2 items-center gap-4 py-10 lg:grid-cols-3">
                            <div class="flex items-center">
                                <img 
                                    src="{{ asset('img/logo.png') }}" 
                                    alt="Logo Isprotik" 
                                    class="h-16 w-auto object-contain hover:scale-105 transition-transform duration-300"
                                >
                                <span class="ml-3 text-2xl font-bold text-white">Isprotik</span>
                            </div>
                            <div class="flex lg:justify-center lg:col-start-2">
                                <h1 class="text-3xl font-bold text-white">Administrador de ISP</h1>
                            </div>
                            @if (Route::has('login'))
                                <nav class="-mx-3 flex flex-1 justify-end">
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="rounded-md px-4 py-2 text-white border border-white transition hover:bg-white hover:text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                            Panel de Control
                                        </a>
                                    @else
                                        <div class="relative">
                                            <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-white border border-white transition hover:bg-white hover:text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                                Iniciar Sesión
                                            </a>
                                        </div>

                                        @if (Route::has('register'))
                                            <div class="relative ml-3">
                                                <a href="{{ route('register') }}" class="rounded-md px-4 py-2 bg-white text-blue-600 transition hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                                    Registrarse
                                                </a>
                                            </div>
                                        @endif
                                    @endauth
                                </nav>
                            @endif
                        </header>

                        <main class="mt-10">
                            <div class="mb-16 text-center">
                                <h2 class="text-4xl font-bold text-white mb-6">Gestión Integral para Proveedores de Internet</h2>
                                <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                                    La solución todo en uno para administrar clientes, facturación, tickets y monitoreo de red en MikroTik.
                                </p>
                            </div>

                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-8">
                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Gestión de Clientes</h3>
                                    <p class="text-blue-100">
                                        Administra toda la información de tus clientes de forma organizada y accesible desde un solo lugar.
                                    </p>
                                </div>

                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Facturación Simple</h3>
                                    <p class="text-blue-100">
                                        Genera facturas automáticas, gestiona pagos y mantén un historial completo de transacciones.
                                    </p>
                                </div>

                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Monitoreo de Nodos</h3>
                                    <p class="text-blue-100">
                                        Supervisa el estado de tu red en tiempo real y recibe alertas ante cualquier incidencia.
                                    </p>
                                </div>

                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Sistema de Tickets</h3>
                                    <p class="text-blue-100">
                                        Atiende las solicitudes de soporte de tus clientes de manera organizada y eficiente.
                                    </p>
                                </div>

                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Planes y Servicios</h3>
                                    <p class="text-blue-100">
                                        Configura y gestiona diferentes planes de servicio con sus características y precios.
                                    </p>
                                </div>

                                <div class="feature-card rounded-lg p-8 text-white border border-white/20">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/10 mb-6">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Integración MikroTik</h3>
                                    <p class="text-blue-100">
                                        Conexión directa con tus routers MikroTik para gestión automatizada de clientes y servicios.
                                    </p>
                                </div>
                            </div>

                            <!-- Nueva Sección: Testimonios y Estadísticas -->
                            <div class="mt-24 mb-16">
                                <div class="text-center mb-12">
                                    <h2 class="text-3xl font-bold text-white mb-4">Lo que dicen nuestros clientes</h2>
                                    <p class="text-xl text-blue-100 max-w-3xl mx-auto">ISP de todo el país confían en Isprotik para gestionar sus redes</p>
                                </div>

                                <div class="grid gap-8 lg:grid-cols-2 mb-16">
                                    <div class="testimonial-card rounded-lg p-8 text-white">
                                        <div class="flex items-center mb-4">
                                            <div class="rounded-full bg-white/20 size-12 flex items-center justify-center mr-4">
                                                <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold">Carlos Méndez</h4>
                                                <p class="text-blue-100 text-sm">ISP Conexión Total, Medellín</p>
                                            </div>
                                        </div>
                                        <p class="italic">"Isprotik ha revolucionado nuestra gestión de clientes. Redujimos el tiempo de facturación en un 70% y nuestros clientes están más satisfechos con el sistema de tickets."</p>
                                    </div>

                                    <div class="testimonial-card rounded-lg p-8 text-white">
                                        <div class="flex items-center mb-4">
                                            <div class="rounded-full bg-white/20 size-12 flex items-center justify-center mr-4">
                                                <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold">María Fernanda Gómez</h4>
                                                <p class="text-blue-100 text-sm">Redes Avanzadas, Bogotá</p>
                                            </div>
                                        </div>
                                        <p class="italic">"La integración con MikroTik es impecable. Ahora podemos gestionar cortes masivos y cambios de planes en minutos, no en horas. Nuestra productividad se ha disparado."</p>
                                    </div>
                                </div>

                                <div class="grid gap-6 md:grid-cols-3 text-center mb-16">
                                    <div class="stats-card rounded-lg p-6 text-white">
                                        <div class="text-4xl font-bold mb-2">+85%</div>
                                        <p class="text-blue-100">Reducción en tiempo de facturación</p>
                                    </div>
                                    <div class="stats-card rounded-lg p-6 text-white">
                                        <div class="text-4xl font-bold mb-2">+50</div>
                                        <p class="text-blue-100">ISPs satisfechos en Colombia & Venezuela</p>
                                    </div>
                                    <div class="stats-card rounded-lg p-6 text-white">
                                        <div class="text-4xl font-bold mb-2">24/7</div>
                                        <p class="text-blue-100">Soporte técnico especializado</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección CTA -->
                            <div class="cta-section rounded-xl p-12 text-center mb-16">
                                <h2 class="text-3xl font-bold text-white mb-6">¿Listo para transformar la gestión de tu ISP?</h2>
                                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Únete a cientos de proveedores que ya optimizaron sus operaciones con Isprotik</p>
                                <div class="flex flex-col sm:flex-row justify-center gap-4">
                                    <a href="#" class="bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-blue-50 transition duration-300">
                                        Prueba Gratis por 14 Días
                                    </a>
                                    <a href="https://wa.me/573215852059" target="_blank" class="bg-transparent border-2 border-white text-white font-semibold py-3 px-8 rounded-lg hover:bg-white hover:text-blue-600 transition duration-300">
                                        Hablar con un Asesor
                                    </a>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-center gap-4">    
                                <img 
                                    src="{{ asset('img/logo.png') }}" 
                                    alt="Logo Isprotik" 
                                    class="h-40 w-auto object-contain hover:scale-105 transition-transform duration-300"
                                >
                            </div>
                            
                        </main>

                        <footer class="py-16 text-center text-sm text-white/80">
                            Isprotik &copy; {{ date('Y') }} - Sistema de Gestión para Proveedores de Internet | 
                            Contáctame: <a href="tel:+573215852059" class="underline hover:text-white">+57 321 5852059</a>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>