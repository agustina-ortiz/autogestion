<div>
    <x-slot:title>Inicio - Sistema Autogestión</x-slot:title>

    @php
        function zerofill($valor, $longitud){
            $res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
            return $res;
        }

        $foto = 'https://autogestion.mercedes.gob.ar/fotos-licencias/fotos-empleados/'.zerofill(auth()->user()->LEGAJO,8).'.jpg';
        $tieneFoto = is_array(@getimagesize($foto));
        
        if (!$tieneFoto) { 
            $foto = asset('images/no-foto.png');
        }
    @endphp

    <!-- Main Container -->
    <main class="w-full translate-y-12">
        <!-- Content Wrapper -->
        <div class="flex gap-8 relative z-10 px-[110px]">
            <!-- Employee Section -->
            <div class="flex-[0_0_40%] flex flex-col bg-white px-16 rounded-2xl translate-y-20 shadow-md overflow-hidden">
                <!-- Sección Superior -->
                <div class="flex items-center gap-6 p-4">
                    <!-- Employee Photo -->
                    <div class="w-[85px] h-[85px] rounded-full bg-white flex items-center justify-center text-white text-5xl font-bold border-2 border-[#77bf43] shadow-lg flex-shrink-0">
                        <img 
                            src="{{ $foto }}" 
                            alt="Foto Empleado" 
                            class="w-[90%] h-[90%] rounded-full object-cover"
                        >
                    </div>

                    <!-- Empleado y Nombre -->
                    <div class="flex flex-col">
                        <p class="text-sm font-semibold text-[#77BF43] uppercase tracking-wide">Empleado</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ auth()->user()->NOMBRE ?? 'Sin nombre' }}</p>
                    </div>
                </div>

                <!-- Barra Divisora Verde -->
                <hr class="border-t-2 border-[#77BF43] mx-1">

                <!-- Sección Inferior - 2 Columnas -->
                <div class="grid grid-cols-2 gap-x-8 px-6 pt-4 pb-6">
                    <!-- Columna Izquierda -->
                    <div class="space-y-3">
                        <p class="text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">DNI: <span class="text-[#333333] font-semibold">{{ auth()->user()->DNI ?? 'Sin DNI' }}</span></strong> 
                        </p>
                        <p class="text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Legajo: <span class="text-[#333333] font-semibold">{{ auth()->user()->LEGAJO ?? 'Sin legajo' }}</span></strong> 
                        </p>
                        @if($cantidadHijos > 0)
                            <p class="text-sm text-gray-800">
                                <strong class="text-[#77BF43] block mb-1">Hijos: <span  class="text-[#333333] font-semibold">{{ $cantidadHijos }}</span></strong> 
                            </p>
                        @endif
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-3">
                        <p class="text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Perfil: <span class="text-[#333333] font-semibold">Empleado</span></strong> 
                        </p>
                        <p class="text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Categoría: <span class="text-[#333333] font-semibold">{{ auth()->user()->CATEGORIA ?? 'Sin categoría' }}</span></strong> 
                        </p>
                        @if($user->esta_inactivo)
                            <p class="text-sm text-gray-800">
                                <strong class="text-[#77BF43] block mb-1">Estado: <span class="text-[#333333] font-semibold">Inactivo</span></strong> 
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Noticias - Solo en Dashboard -->
        @php
            $noticia = DB::table('in_noticia')
                ->where('FECHAVTO', '>=', now())
                ->orderBy('FECHA', 'desc')
                ->first();
        @endphp

        @if($noticia)
            <div class="grid grid-cols-2 -mt-20 -mx-12">
                <!-- Columna Izquierda -->
                <div>
                    <!-- Parte Superior Verde (vacía) -->
                    <div class="bg-[#77BF43] px-6 py-4 h-1/3">
                        <!-- Espacio vacío para alinearse con el título -->
                        <div class="h-full"></div>
                    </div>
                    <!-- Parte Inferior Blanca (vacía) -->
                    <div class="bg-white px-6 py-6">
                        <!-- Espacio vacío -->
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div>
                    <!-- Parte Superior Verde: Título de la Noticia -->
                    <div class="bg-[#77BF43] px-6 py-4 h-1/3 flex items-center">
                        <div class="flex justify-between w-full">
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                {{ $noticia->TITULO }}
                            </h2>
                            <span class="text-xs text-white bg-white bg-opacity-20 px-2 py-1 rounded-full h-fit">
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
                                class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Buttons Grid -->
        <div class="grid grid-cols-4 gap-x-8 relative z-10 px-[110px] -mt-5">
            <!-- Recibos -->
            <a href="{{ route('recibos') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">RECIBOS</span>
            </a>

            <!-- Asistencias -->
            <a href="{{ route('asistencias') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">ASISTENCIAS</span>
            </a>

            <!-- Compensatorios -->
            <a href="{{ route('compensatorios') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <svg class="w-10 h-10 scale-110 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">COMPENSATORIOS</span>
            </a>

            <!-- Solicitudes -->
            <a href="{{ route('solicitudes') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">SOLICITUDES</span>
            </a>

            <!-- Hijos -->
            @if($cantidadHijos > 0)
                <a href="{{ route('hijos') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">HIJOS/AS</span>
                </a>
            @endif

            
            @if($esJubilado)
                <!-- DDJJ Jubilados -->
                <a href="{{ route('anticipo.jubilatorio') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">DDJJ para jubilados</span>
                </a>
            @endif
        </div>
    </main>
</div>