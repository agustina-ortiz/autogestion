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
    <main class="overflow-hidden relative">
        <!-- Content Wrapper -->
        <div class="flex gap-8 relative z-10 px-[110px]">
            <!-- Employee Section -->
            <div class="flex-[0_0_40%] flex flex-col bg-white px-16 rounded-2xl shadow-md overflow-hidden">
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
            <!-- Welcome Section -->
            <div class="flex-1 flex flex-col justify-center items-end">
                <h1 class="text-white text-left text-[2.5rem] font-bold">
                    <span class="block">AUTOGESTIÓN</span>
                    <span class="block -mt-4">RECURSOS HUMANOS</span>
                </h1>
            </div>
        </div>

        <!-- Buttons Grid -->
        <div class="grid grid-cols-4 gap-x-8 mt-auto relative z-10 px-[110px]">
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

            <!-- Planillas -->
            @if($cantidadHijos > 0)
                <a href="{{ route('planillas') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <svg class="w-10 h-10 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">PLANILLAS</span>
                </a>
                
                <!-- DDJJ Asignaciones -->
                <a href="{{ route('asignaciones.familiares') }}" class="bg-[#a4d6e7] rounded-xl flex flex-row items-center justify-start gap-3 px-8 py-8 transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full h-2/3">
                    <span class="ml-2 font-bold text-[1rem] text-[#333333]">DDJJ para asignaciones familiares para madres</span>
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