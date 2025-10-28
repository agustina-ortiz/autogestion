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

        {{-- Sección de información general --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6">
                <h2 class="text-xl font-bold m-0 uppercase">Información Importante</h2>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        A partir del mes de <span class="font-semibold">{{ $mesActual }}</span> de <span class="font-semibold">{{ $anioActual }}</span>, el tope máximo a solicitar por adelantos de sueldo es de <span class="font-semibold">${{ number_format($montoMaximoAdelanto, 0, ',', '.') }}</span>. La única vía de solicitud es por ésta página de <span class="font-semibold">AUTOGESTIÓN</span>. No se puede <span class="font-semibold">MODIFICAR EL MONTO MÁXIMO</span>. Lo que no se encuentre debidamente solicitado <span class="font-semibold">NO SE ABONARÁ</span> por fuera de ninguna liquidación.
                    </p>
                </div>
            </div>
        </div>

        {{-- Sección de solicitud de adelanto --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6">
                <h2 class="text-xl font-bold m-0 uppercase">Solicitud de Adelanto</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-gray-700 text-base">
                        <span class="font-semibold">Fecha actual:</span> {{ $fechaActual }}
                    </p>
                </div>

                @if(!$puedesolicitarAdelanto)
                    {{-- Mensaje de error cuando no se puede solicitar --}}
                    <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded mb-6">
                        <p class="text-sm text-red-700 leading-relaxed">
                            <span class="font-bold text-red-800">¡ATENCIÓN!</span> No es posible solicitar un adelanto. La fecha inicial es <span class="font-semibold">{{ $fechaInicial }}</span> y la fecha límite es el <span class="font-semibold">{{ $fechaLimite }}</span>.
                        </p>
                    </div>
                @else
                    {{-- Formulario cuando sí se puede solicitar --}}
                    <div class="bg-green-50 border-l-4 border-green-500 p-5 rounded mb-6">
                        <p class="text-sm text-green-700 leading-relaxed">
                            <span class="font-bold text-green-800">✓ Período habilitado</span> para solicitar adelantos. Puede realizar su solicitud hasta el día <span class="font-semibold">{{ $fechaLimite }}</span>.
                        </p>
                    </div>

                    {{-- Mensaje de éxito --}}
                    @if (session()->has('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Mensaje de error --}}
                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Monto a solicitar
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                                <input 
                                    type="number" 
                                    wire:model="montoSolicitado"
                                    max="{{ $montoMaximoAdelanto }}"
                                    min="1"
                                    step="0.01"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none"
                                    placeholder="Ingrese el monto">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Monto máximo: ${{ number_format($montoMaximoAdelanto, 2, ',', '.') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Observaciones (opcional)
                            </label>
                            <textarea 
                                wire:model="observaciones"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none resize-none"
                                placeholder="Ingrese alguna observación si lo desea"></textarea>
                        </div>

                        <button 
                            wire:click="confirmarSolicitud"
                            class="w-full bg-gradient-to-r from-[#77BF43] to-[#5da832] text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                            Confirmar Solicitud de Adelanto
                        </button>
                    </div>
                @endif
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