<x-layouts.autogestion>
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
    <main class="overflow-hidden">
        <!-- Content Wrapper -->
        <div class="flex gap-8">
            <!-- Employee Section -->
            <div class="flex-[0_0_30%] flex flex-col items-center bg-gradient-to-br from-gray-50 to-gray-200 p-6 rounded-2xl shadow-md">
                <!-- Employee Photo -->
                <div class="w-[120px] h-[120px] rounded-full bg-gradient-to-br from-[#91D5E2] to-[#77BF43] mb-4 flex items-center justify-center text-white text-5xl font-bold border-4 border-white shadow-lg">
                    <img 
                        src="{{ $foto }}" 
                        alt="Foto Empleado" 
                        class="w-full h-full rounded-full object-cover {{ !$tieneFoto ? 'brightness-0 invert' : '' }}"
                    >
                </div>

                <!-- Employee Info -->
                <div class="w-full text-left">
                    <p class="my-2 text-[0.95rem] text-gray-800">
                        <strong class="text-[#77BF43] inline-block w-20">Empleado:</strong> 
                        {{ auth()->user()->NOMBRE ?? 'Sin nombre' }}
                    </p>
                    <p class="my-2 text-[0.95rem] text-gray-800">
                        <strong class="text-[#77BF43] inline-block w-20">DNI:</strong> 
                        {{ auth()->user()->DNI ?? 'Sin DNI' }}
                    </p>
                    <p class="my-2 text-[0.95rem] text-gray-800">
                        <strong class="text-[#77BF43] inline-block w-20">Legajo:</strong> 
                        {{ auth()->user()->LEGAJO ?? 'Sin legajo' }}
                    </p>
                    <p class="my-2 text-[0.95rem] text-gray-800">
                        <strong class="text-[#77BF43] inline-block w-20">Perfil:</strong> 
                        Empleado
                    </p>
                    <p class="my-2 text-[0.95rem] text-gray-800">
                        <strong class="text-[#77BF43] inline-block w-20">Categoría:</strong> 
                        {{ auth()->user()->CATEGORIA ?? 'Sin categoría' }}
                    </p>
                    @if(auth()->user()->HIJOS)
                        <p class="my-2 text-[0.95rem] text-gray-800">
                            <strong class="text-[#77BF43] inline-block w-20">Hijos:</strong> 
                            {{ auth()->user()->HIJOS}}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="flex-1 bg-gradient-to-br from-white to-gray-50 p-8 rounded-2xl shadow-md flex flex-col justify-center">
                <h1 class="text-[#77BF43] mb-4 text-[1.8rem] font-bold">
                    Bienvenido al sistema AUTOGESTIÓN
                </h1>
                <p class="text-gray-600 leading-relaxed text-base">
                    En esta página los empleados de la <strong class="text-[#91D5E2]">Municipalidad de Mercedes</strong>, podrán consultar 
                    sus <strong class="text-[#91D5E2]">recibos de sueldos</strong> como así también, otros temas de interés.
                </p>
                <p class="text-gray-600 leading-relaxed text-base mt-4">
                    Si tiene dudas, puede consultar la sección de <strong class="text-[#91D5E2]">Preguntas frecuentes</strong>
                </p>
                <p class="text-gray-600 leading-relaxed text-base mt-2">
                    Si necesita informar algo puede dirigirse a la sección <strong class="text-[#91D5E2]">Contacto</strong>
                </p>
            </div>
        </div>

        <!-- Buttons Grid -->
        <div class="grid grid-cols-4 gap-12 mt-auto">
            <!-- Recibos -->
            <a href="{{ route('recibos') }}" class="action-card">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span>Recibos</span>
            </a>

            <!-- Asistencias -->
            <a href="{{ route('asistencias') }}" class="bg-gradient-to-br from-[#77BF43] to-[#BED630] text-white p-4 px-2 rounded-xl flex flex-col items-center gap-2 text-center transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full">
                <svg class="w-8 h-8 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="font-semibold text-[0.9rem] text-white">Asistencias</span>
            </a>

            <!-- Compensatorios -->
            <a href="#" class="bg-gradient-to-br from-[#77BF43] to-[#BED630] text-white p-4 px-2 rounded-xl flex flex-col items-center gap-2 text-center transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full">
                <svg class="w-8 h-8 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span class="font-semibold text-[0.9rem] text-white">Compensatorios</span>
            </a>

            <!-- Solicitudes -->
            <a href="#" class="bg-gradient-to-br from-[#77BF43] to-[#BED630] text-white p-4 px-2 rounded-xl flex flex-col items-center gap-2 text-center transition-all duration-300 shadow-md hover:-translate-y-1 hover:shadow-xl no-underline cursor-pointer border-0 w-full">
                <svg class="w-8 h-8 stroke-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                <span class="font-semibold text-[0.9rem] text-white">Solicitudes</span>
            </a>
        </div>
    </main>
</x-layouts.autogestion>