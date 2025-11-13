<div>
    <x-slot:title>Inicio - Sistema Autogestión</x-slot:title>

    @php
        use Illuminate\Support\Facades\Storage;
        
        function zerofill($valor, $longitud){
            $res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
            return $res;
        }

        $legajo = zerofill(auth()->user()->LEGAJO, 8);
        $nombreArchivo = $legajo . '.jpg';
        $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
        
        // Si existe el marcador de foto eliminada, mostrar imagen por defecto
        if (Storage::disk('public')->exists($marcadorEliminada)) {
            $foto = asset('images/no-foto.png');
            $tieneFoto = false;
        } else {
            // Primero verificar si existe en storage local
            if (Storage::disk('public')->exists('fotos-empleados/' . $nombreArchivo)) {
                $foto = asset('storage/fotos-empleados/' . $nombreArchivo);
                $tieneFoto = true;
            } else {
                // Si no existe localmente, buscar en el servidor remoto
                $foto = 'https://autogestion.mercedes.gob.ar/fotos-licencias/fotos-empleados/' . $legajo . '.jpg';
                $tieneFoto = is_array(@getimagesize($foto));
                
                if (!$tieneFoto) { 
                    $foto = asset('images/no-foto.png');
                }
            }
        }
    @endphp

    @php
        $noticia = DB::table('in_noticia')
            ->where('FECHAVTO', '>=', now())
            ->orderBy('FECHA', 'desc')
            ->first();
    @endphp

    <!-- Main Container -->
    <main class="w-full mt-4 translate-y-12 md:translate-y-12 translate-y-4">
        <!-- Content Wrapper -->
        <div class="flex md:flex-row flex-col gap-4 md:gap-8 relative z-10 px-4 md:px-[110px]">
            <!-- Employee Section -->
            <div class="flex-none md:flex-[0_0_40%] w-full flex flex-col bg-white px-4 md:px-16 pt-4 rounded-2xl shadow-md overflow-hidden {{ $noticia ? 'md:translate-y-20' : 'translate-y-0' }}">
                <!-- Sección Superior -->
                <div class="flex items-center gap-3 md:gap-6 p-2 md:p-4">
                    <!-- Employee Photo -->
                    <div class="w-[60px] h-[60px] md:w-[85px] md:h-[85px] rounded-full bg-white flex items-center justify-center text-white text-5xl font-bold border-2 border-[#77bf43] shadow-lg flex-shrink-0">
                        <img 
                            src="{{ $foto }}" 
                            alt="Foto Empleado" 
                            class="w-[90%] h-[90%] rounded-full object-cover"
                        >
                    </div>

                    <!-- Empleado y Nombre -->
                    <div class="flex flex-col">
                        <p class="text-xs md:text-sm font-semibold text-[#77BF43] uppercase tracking-wide">Empleado</p>
                        <p class="text-base md:text-xl font-bold text-gray-800 mt-1">{{ auth()->user()->NOMBRE ?? 'Sin nombre' }}</p>
                    </div>
                </div>

                <!-- Barra Divisora Verde -->
                <hr class="border-t-2 border-[#77BF43] mx-1">

                <!-- Sección Inferior - 2 Columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 md:gap-x-8 px-3 md:px-6 pt-3 md:pt-4 pb-4 md:pb-6">
                    <!-- Columna Izquierda -->
                    <div class="space-y-2 md:space-y-3">
                        <p class="text-xs md:text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">DNI: <span class="text-[#333333] font-semibold">{{ auth()->user()->DNI ?? 'Sin DNI' }}</span></strong> 
                        </p>
                        <p class="text-xs md:text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Legajo: <span class="text-[#333333] font-semibold">{{ auth()->user()->LEGAJO ?? 'Sin legajo' }}</span></strong> 
                        </p>
                        @if($cantidadHijos > 0)
                            <p class="text-xs md:text-sm text-gray-800">
                                <strong class="text-[#77BF43] block mb-1">Hijos: <span  class="text-[#333333] font-semibold">{{ $cantidadHijos }}</span></strong> 
                            </p>
                        @endif
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-2 md:space-y-3">
                        <p class="text-xs md:text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Perfil: <span class="text-[#333333] font-semibold">Empleado</span></strong> 
                        </p>
                        <p class="text-xs md:text-sm text-gray-800">
                            <strong class="text-[#77BF43] block mb-1">Categoría: <span class="text-[#333333] font-semibold">{{ auth()->user()->CATEGORIA ?? 'Sin categoría' }}</span></strong> 
                        </p>
                        @if($user->esta_inactivo)
                            <p class="text-xs md:text-sm text-gray-800">
                                <strong class="text-[#77BF43] block mb-1">Estado: <span class="text-[#333333] font-semibold">Inactivo</span></strong> 
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Noticias - Solo en Dashboard -->
        @if($noticia)
            <div class="grid grid-cols-1 md:grid-cols-2 mt-4 md:-mt-24 mx-4 md:-mx-12">
                <!-- Columna Izquierda - Solo visible en desktop -->
                <div class="hidden md:block">
                    <!-- Parte Superior Verde (vacía) -->
                    <div class="bg-[#ed5b9a] px-6 py-4 h-1/4">
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
                    <div class="bg-[#ed5b9a] px-4 md:px-6 py-3 md:py-4 md:h-1/4 flex items-center rounded-t-xl md:rounded-none">
                        <div class="flex flex-col md:flex-row md:justify-between w-full gap-2 md:gap-0">
                            <h2 class="text-base md:text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                {{ $noticia->TITULO }}
                            </h2>
                            <span class="text-xs text-white bg-white bg-opacity-20 px-2 py-1 rounded-full h-fit w-fit">
                                {{ \Carbon\Carbon::parse($noticia->FECHA)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Parte Inferior Blanca: Contenido de la Noticia -->
                    <div class="bg-white px-4 md:px-6 py-4 md:py-6 rounded-b-xl md:rounded-none">
                        <div class="text-xs md:text-sm text-gray-700 leading-relaxed">
                            {!! nl2br(e($noticia->DETALLE)) !!}
                        </div>

                        @if($noticia->LINK)
                            <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                                <a href="{{ $noticia->LINK }}" 
                                target="_blank"
                                class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-xs md:text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Ver más información
                                </a>
                            </div>
                        @endif

                        @if($noticia->ARCHIVO)
                            <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                                <a href="{{ asset('storage/noticias/' . $noticia->ARCHIVO) }}" 
                                target="_blank"
                                class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors text-xs md:text-sm">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-x-8 relative z-10 px-4 md:px-[110px] mt-4 md:-mt-3 pb-4">
            <!-- Recibos -->
            <a href="{{ route('recibos') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <img src="{{ asset('images/Recurso8.png') }}" class="w-10 h-10" alt="">
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">RECIBOS</span>
            </a>

            <!-- Asistencias -->
            <a href="{{ route('asistencias') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <img src="{{ asset('images/Recurso7.png') }}" class="w-10 h-10" alt="">
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">ASISTENCIAS</span>
            </a>

            <!-- Compensatorios -->
            <a href="{{ route('compensatorios') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <img src="{{ asset('images/Recurso5.png') }}" class="w-10 h-10" alt="">
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">COMPENSATORIOS</span>
            </a>

            <!-- Solicitudes -->
            <a href="{{ route('solicitudes') }}" class="bg-[#bdd632] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                <img src="{{ asset('images/Recurso6.png') }}" class="w-10 h-10" alt="">
                <span class="ml-2 font-bold text-[1rem] text-[#333333]">SOLICITUDES</span>
            </a>

            <!-- Hijos -->
            @if($cantidadHijos > 0)
                <a href="{{ route('hijos') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <img src="{{ asset('images/Recurso13.png') }}" class="w-8 h-10" alt="">
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">HIJOS/AS</span>
                </a>
            @endif

            
            @if($esJubilado)
                <!-- DDJJ Jubilados -->
                <a href="{{ route('anticipo.jubilatorio') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <img src="{{ asset('images/Recurso14.png') }}" class="w-10 h-10" alt="">
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">JUBILADOS/AS</span>
                </a>
            @endif
        </div>
    </main>
</div>