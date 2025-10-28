<div class="min-h-screen">
    <div class="p-8 max-w-[1400px] mx-auto">

        {{-- Header con nombre de usuario --}}
        <div class="mb-6">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6 rounded-xl shadow-[0_2px_8px_rgba(119,191,67,0.3)]">
                <h3 class="text-xl font-semibold m-0">
                    Bienvenido/a, {{ Auth::user()->NOMBRE }}
                </h3>
            </div>
        </div>

        {{-- Mensaje de éxito --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Sección principal de solicitud --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6">
                <h2 class="text-xl font-bold m-0 uppercase">Solicitud de Sueldo por CHEQUE</h2>
            </div>
            <div class="p-6">
                {{-- Fecha actual --}}
                <div class="mb-6">
                    <p class="text-gray-700 text-base">
                        <span class="font-semibold">Fecha actual:</span> {{ $fechaActual }}
                    </p>
                </div>

                {{-- Información importante --}}
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        Las solicitudes de sueldo por cheque se realizarán <span class="font-semibold">únicamente por Autogestión</span>.
                    </p>
                </div>

                {{-- Formulario --}}
                <div class="space-y-6">
                    {{-- Apellido y Nombre --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Apellido y Nombre
                        </label>
                        <input 
                            type="text" 
                            value="{{ $nombreCompleto }}"
                            readonly
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                            disabled>
                    </div>

                    {{-- Legajo --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Legajo
                        </label>
                        <input 
                            type="text" 
                            value="{{ $legajo }}"
                            readonly
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                            disabled>
                    </div>

                    {{-- Forma de cobro --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Seleccione forma de cobro
                        </label>
                        <select 
                            wire:model="formaCobro"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none">
                            <option value="">- Seleccione una opción -</option>
                            <option value="deposito">Por Depósito en su cuenta sueldo</option>
                            <option value="cheque">Por Cheque</option>
                        </select>
                    </div>

                    {{-- Mensaje importante --}}
                    <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded">
                        <p class="text-sm text-red-700 leading-relaxed">
                            <span class="font-bold text-red-800">¡IMPORTANTE!</span> Únicamente los empleados que soliciten el cobro del sueldo por <span class="font-semibold">CHEQUE</span>, deben indicarlo en autogestión. SI NO LO HICIERAN, EL PAGO DEL MISMO SE HARÁ EN LA CUENTA SUELDOS DEL <span class="font-semibold">BANCO PROVINCIA</span>. <span class="font-semibold">SIN EXCEPCIÓN</span>.
                        </p>
                    </div>

                    {{-- Botón confirmar --}}
                    <div class="flex justify-center">
                        <button 
                            wire:click="confirmarSolicitud"
                            class="w-1/2 bg-gradient-to-r from-[#77BF43] to-[#5da832] text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                            Confirmar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón Volver --}}
        <div class="flex justify-center">
            <a 
                href="{{ route('solicitudes') }}" 
                class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Solicitudes
            </a>
        </div>

    </div>
</div>