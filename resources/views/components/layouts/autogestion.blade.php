<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Sistema Autogestión - Mercedes' }}</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }   
        </style>
    </head>
    <body>
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-[#BED630] shadow-md flex justify-between items-center px-4 md:px-40 py-3 md:py-4">
            <div class="logo">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Municipalidad" class="h-8 md:h-auto">
                </a>
            </div>
            <div class="hidden md:block">
                <h1 class="text-xl font-bold text-white">AUTOGESTIÓN RECURSOS HUMANOS</h1>
            </div>
            <div class="md:hidden flex-1 text-center">
                <h1 class="text-sm font-bold text-white">AUTOGESTIÓN</h1>
            </div>
            <div class="header-right flex items-center gap-3">
                <!-- Dropdown de Mi Perfil -->
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open"
                        class="bg-black text-[#bdd632] rounded-full px-2 md:px-3 py-1.5 md:py-2 hover:bg-gray-800 transition-colors flex items-center gap-1 md:gap-2 text-xs font-bold"
                    >
                        <img src="{{ asset('images/icono-perfil.png') }}" class="w-4 h-4 md:w-5 md:h-5 rounded-full text-[#bdd632]" alt="Foto de Perfil">
                        <span class="hidden md:inline">Mi Perfil</span>
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
        <main class="flex-1 pt-16 md:pt-10">
            <!-- Imagen de fondo que ocupa el 45% superior - Solo en Dashboard y Desktop -->
            @if(request()->routeIs('dashboard'))
                <div class="hidden md:block absolute top-0 left-0 right-0 h-[43%] z-0">
                    <img 
                        src="{{ asset('images/fondo.png') }}" 
                        alt="Fondo" 
                        class="w-full h-[113%] object-cover"
                    >
                </div>
            @endif
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bottom-0 left-0 right-0 z-50 flex flex-col md:flex-row justify-between items-center p-4 px-4 md:px-40 bg-[#333333] gap-4 md:gap-0">
            
            <!-- Lado izquierdo -->
            <div class="flex flex-col gap-2 items-center md:items-start">
                <h3 class="text-white font-semibold text-xs md:text-sm">Municipalidad de Mercedes</h3>
                <div class="flex gap-3">
                    <!-- Facebook -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-3.5 md:h-4 w-3.5 md:w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-3.5 md:h-4 w-3.5 md:w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <!-- YouTube -->
                    <a href="#" class="text-white hover:opacity-80 transition-opacity">
                        <svg class="h-3.5 md:h-4 w-3.5 md:w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Lado derecho -->
            <div class="flex flex-col md:flex-row items-center gap-3 md:gap-6">
                <a href="{{ route('preguntas.frecuentes') }}" class="bg-white text-black rounded-full px-3 py-1 hover:bg-gray-100 transition-colors text-xs font-bold whitespace-nowrap">
                    Preguntas Frecuentes
                </a>
                <a href="https://wa.me/5491234567890" class="flex items-center gap-2 text-white hover:opacity-80 transition-opacity">
                    <svg class="h-5 md:h-6 w-5 md:w-6 text-[#bdd632]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span class="font-semibold text-xs md:text-sm">Contactate</span>
                </a>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>