<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Sistema Autogestión - Mercedes' }}</title>

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#BED630">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Autogestión">
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            /* Estilos de impresión */
            @media print {
                header,
                footer,
                nav,
                .no-print {
                    display: none !important;
                }
                
                main {
                    padding-top: 0 !important;
                }
            }
        </style>
        
        @php
            $noticia = DB::table('in_noticia')
                ->where('FECHAVTO', '>=', now())
                ->orderBy('FECHA', 'desc')
                ->first();
        @endphp
    </head>
    <body>
        <!-- Header -->
        <header class="no-print fixed top-0 left-0 right-0 z-50 bg-[#BED630] shadow-md flex justify-between items-center px-3 sm:px-6 md:px-20 lg:px-40 py-2 md:py-3 lg:py-4">
            <div class="logo">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Municipalidad" class="h-7 sm:h-9 md:h-10 lg:h-12">
                </a>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('dashboard') }}"><h1 class="text-base md:text-lg lg:text-xl font-bold text-white whitespace-nowrap">AUTOGESTIÓN RECURSOS HUMANOS</h1></a>
            </div>
            <div class="header-right flex items-center gap-2 md:gap-3">
                <!-- Dropdown de Mi Perfil -->
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open"
                        class="bg-black text-[#bdd632] rounded-full px-2 sm:px-2.5 md:px-3 py-1.5 md:py-2 hover:bg-gray-800 transition-colors flex items-center gap-1 md:gap-2 text-xs font-bold whitespace-nowrap"
                    >
                        <img src="{{ asset('images/icono-perfil.png') }}" class="w-4 h-4 md:w-5 md:h-5 rounded-full text-[#bdd632]" alt="Foto de Perfil">
                        <span class="hidden sm:inline text-[10px] md:text-xs">Mi Perfil</span>
                        <svg class="w-3 h-3 md:w-4 md:h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                        style="display: none;"
                    >
                        <a 
                            href="{{ route('perfil') }}" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Actualizar mis Datos
                        </a>

                        <hr class="my-1">

                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 pt-20 sm:pt-14 md:pt-16 lg:pt-14">
            <!-- Imagen de fondo que ocupa el 45% superior - Solo en Dashboard y Desktop -->
            @if(request()->routeIs('dashboard'))
                <div class="hidden lg:block absolute top-0 left-0 right-0 h-[43%] z-0">
                    <img 
                        src="{{ asset('images/Recurso12.png') }}" 
                        alt="Fondo" 
                        class="w-full h-full object-cover"
                    >
                </div>

                <!-- Sección de Noticias Desktop - Posicionada sobre el fondo -->
                @if($noticia)
                    <div class="hidden lg:grid grid-cols-2 absolute top-[43%] left-0 right-0 z-10">
                        <!-- Columna Izquierda - Vacía -->
                        <div>
                            <!-- Parte Superior Rosa (vacía) -->
                            <div class="bg-[#ed5b9a] px-6 py-4 h-1/4">
                                <div class="h-full"></div>
                            </div>
                            <!-- Parte Inferior Blanca (vacía) -->
                            <div class="bg-white px-6 py-6">
                                <!-- Espacio vacío -->
                            </div>
                        </div>

                        <!-- Columna Derecha - Contenido de la Noticia -->
                        <div>
                            <!-- Parte Superior Rosa: Título de la Noticia -->
                            <div class="bg-[#ed5b9a] px-6 py-4 h-1/4 flex items-center">
                                <div class="flex flex-row justify-between w-full">
                                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        {{ $noticia->TITULO }}
                                    </h2>
                                    <span class="text-xs text-white bg-white bg-opacity-20 px-2 py-1 rounded-full h-fit whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($noticia->FECHA)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Parte Inferior Blanca: Contenido de la Noticia -->
                            <div class="bg-white px-6 py-6">
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    {!! nl2br(e($noticia->DETALLE)) !!}
                                </div>

                                @if($noticia->LINK)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <a href="{{ $noticia->LINK }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-sm break-all">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Ver más información
                                        </a>
                                    </div>
                                @endif

                                @if($noticia->ARCHIVO)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <a href="{{ asset('storage/noticias/' . $noticia->ARCHIVO) }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-sm break-all">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Descargar archivo adjunto
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="md:pt-10">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer Desktop - Oculto en Mobile -->
        <footer class="no-print hidden lg:flex bottom-0 left-0 right-0 z-50 flex-row justify-between items-center p-4 px-20 lg:px-40 bg-[#333333] {{ $noticia ? 'lg:translate-y-20' : '' }}">
            
            <!-- Lado izquierdo -->
            <div class="flex flex-col gap-2 items-start">
                <h3 class="text-white font-semibold text-sm">Municipalidad de Mercedes</h3>
                <div class="flex gap-3">
                    <!-- Facebook -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <!-- YouTube -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Lado derecho -->
            <div class="flex flex-row items-center gap-4 lg:gap-6">
                <a href="{{ route('preguntas.frecuentes') }}" class="bg-white text-black rounded-full px-3 py-1.5 hover:bg-gray-100 transition-colors text-xs font-bold whitespace-nowrap">
                    Preguntas Frecuentes
                </a>
                <a href="https://wa.me/5491234567890" class="flex items-center gap-2 text-white hover:opacity-80 transition-opacity">
                    <svg class="h-6 w-6 text-[#bdd632]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span class="font-semibold text-sm">Contactate</span>
                </a>
            </div>
        </footer>

        <!-- Bottom Navigation Mobile - Estilo App -->
        <nav class="mobile-footer no-print lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 shadow-lg">
            <div class="flex justify-around items-center py-2">
                <!-- Inicio/Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('dashboard') ? 'text-[#77BF43]' : 'text-gray-600' }}">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[9px] sm:text-[10px] mt-1 font-semibold">Inicio</span>
                </a>

                <!-- Recibos -->
                <a href="{{ route('recibos') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('recibos') ? 'text-[#77BF43]' : 'text-gray-600' }}">
                    <img src="{{ request()->routeIs('recibos') ? asset('images/recibosactivo.png') : asset('images/recibosmobile.png') }}" class="w-5 h-5 sm:w-6 sm:h-6" alt="">
                    <span class="text-[9px] sm:text-[10px] mt-1 font-semibold">Recibos</span>
                </a>

                <!-- Asistencias -->
                <a href="{{ route('asistencias') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('asistencias') ? 'text-[#77BF43]' : 'text-gray-600' }}">
                    <img src="{{ request()->routeIs('asistencias') ? asset('images/asistenciasactivo.png') : asset('images/asistenciasmobile.png') }}" class="w-5 h-5 sm:w-6 sm:h-6" alt="">
                    <span class="text-[9px] sm:text-[10px] mt-1 font-semibold">Asistencias</span>
                </a>

                <!-- Compensatorios -->
                <a href="{{ route('compensatorios') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('compensatorios') ? 'text-[#77BF43]' : 'text-gray-600' }}">
                    <img src="{{ request()->routeIs('compensatorios') ? asset('images/compensatoriosactivo.png') : asset('images/compensatoriosmobile.png') }}" class="w-5 h-5 sm:w-6 sm:h-6" alt="">
                    <span class="text-[9px] sm:text-[10px] mt-1 font-semibold">Compensatorios</span>
                </a>

                <!-- Solicitudes -->
                <a href="{{ route('solicitudes') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('solicitudes') ? 'text-[#77BF43]' : 'text-gray-600' }}">
                    <img src="{{ request()->routeIs('solicitudes') ? asset('images/solicitudesactivo.png') : asset('images/solicitudesmobile.png') }}" class="w-5 h-5 sm:w-6 sm:h-6" alt="">
                    <span class="text-[9px] sm:text-[10px] mt-1 font-semibold">Solicitudes</span>
                </a>
            </div>
        </nav>

        @livewireScripts
        @stack('scripts')

        <!-- PWA Install Button and Service Worker -->
        <div
            x-data="pwaInstall()"
            x-cloak
        >
            <!-- Botón para Android/Chrome -->
            <div
                x-show="showInstallButton && !isIOS"
                class="fixed top-16 md:top-20 left-1/2 transform -translate-x-1/2 z-[60]"
            >
                <button
                    @click="installPWA()"
                    class="flex items-center gap-3 bg-[#e63946] hover:bg-[#c1121f] text-white font-bold py-3 px-6 rounded-full shadow-2xl transition-all duration-300 animate-bounce hover:animate-none border-2 border-white"
                >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="text-base md:text-lg">Instalar App</span>
                    <button
                        @click.stop="dismissInstall()"
                        class="ml-2 hover:bg-white/20 rounded-full p-1"
                        title="Cerrar"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </button>
            </div>

            <!-- Botón para iOS -->
            <div
                x-show="showIOSInstall"
                class="fixed top-16 md:top-20 left-1/2 transform -translate-x-1/2 z-[60]"
            >
                <button
                    @click="showIOSModal = true"
                    class="flex items-center gap-3 bg-[#e63946] hover:bg-[#c1121f] text-white font-bold py-3 px-6 rounded-full shadow-2xl transition-all duration-300 animate-bounce hover:animate-none border-2 border-white"
                >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="text-base md:text-lg">Instalar App</span>
                    <button
                        @click.stop="dismissInstall()"
                        class="ml-2 hover:bg-white/20 rounded-full p-1"
                        title="Cerrar"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </button>
            </div>

            <!-- Modal de instrucciones para iOS -->
            <div
                x-show="showIOSModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50 z-[70] flex items-end sm:items-center justify-center p-4"
                @click.self="showIOSModal = false"
            >
                <div
                    class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-md p-6 shadow-2xl"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Instalar Autogestión</h3>
                        <button @click="showIOSModal = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <p class="text-gray-600 mb-6">Para instalar la app en tu iPhone o iPad, sigue estos pasos:</p>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#e63946] text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <div class="flex-1">
                                <p class="text-gray-700">Toca el botón <strong>Compartir</strong></p>
                                <div class="mt-2 flex items-center gap-2 text-blue-500">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L12 14M12 2L8 6M12 2L16 6M4 12V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V12"/>
                                    </svg>
                                    <span class="text-sm">(icono en la barra inferior de Safari)</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#e63946] text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <div class="flex-1">
                                <p class="text-gray-700">Busca y toca <strong>"Agregar a pantalla de inicio"</strong></p>
                                <div class="mt-2 flex items-center gap-2 text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-sm">Agregar a pantalla de inicio</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#e63946] text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <div class="flex-1">
                                <p class="text-gray-700">Toca <strong>"Agregar"</strong> en la esquina superior derecha</p>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="showIOSModal = false; dismissInstall();"
                        class="mt-6 w-full bg-[#e63946] hover:bg-[#c1121f] text-white font-bold py-3 px-6 rounded-full transition-colors"
                    >
                        Entendido
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Registrar Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then(registration => {
                            console.log('Service Worker registrado:', registration.scope);
                        })
                        .catch(error => {
                            console.log('Error al registrar Service Worker:', error);
                        });
                });
            }

            // Alpine.js component para PWA Install
            function pwaInstall() {
                return {
                    deferredPrompt: null,
                    showInstallButton: false,
                    showIOSInstall: false,
                    showIOSModal: false,
                    isIOS: false,

                    init() {
                        // Detectar iOS
                        this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) ||
                                     (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                        // Verificar si ya fue descartado en esta sesión
                        if (sessionStorage.getItem('pwaInstallDismissed')) {
                            return;
                        }

                        // Verificar si ya está en modo standalone (ya instalada)
                        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                                            window.navigator.standalone === true;

                        if (isStandalone) {
                            return;
                        }

                        // Para iOS: mostrar botón con instrucciones
                        if (this.isIOS) {
                            // Solo mostrar en Safari (no en Chrome iOS, etc.)
                            const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);
                            if (isSafari) {
                                this.showIOSInstall = true;
                            }
                            return;
                        }

                        // Para Android/Chrome: usar beforeinstallprompt
                        window.addEventListener('beforeinstallprompt', (e) => {
                            e.preventDefault();
                            this.deferredPrompt = e;
                            this.showInstallButton = true;
                        });

                        // Ocultar si ya está instalado
                        window.addEventListener('appinstalled', () => {
                            this.showInstallButton = false;
                            this.deferredPrompt = null;
                        });
                    },

                    async installPWA() {
                        if (!this.deferredPrompt) return;

                        this.deferredPrompt.prompt();
                        const { outcome } = await this.deferredPrompt.userChoice;

                        if (outcome === 'accepted') {
                            console.log('PWA instalada');
                        }

                        this.deferredPrompt = null;
                        this.showInstallButton = false;
                    },

                    dismissInstall() {
                        this.showInstallButton = false;
                        this.showIOSInstall = false;
                        sessionStorage.setItem('pwaInstallDismissed', 'true');
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>