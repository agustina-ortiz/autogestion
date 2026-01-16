<div class="pb-12 sm:p-0">
    <div class="max-w-[1400px] mx-auto">

        {{-- Header con nombre de usuario --}}
        <div class="mb-6">
            <div class="bg-[#77BF43] text-white p-4 px-4 sm:px-6 rounded-xl shadow-[0_2px_8px_rgba(119,191,67,0.3)]">
                <h3 class="text-lg sm:text-xl font-semibold m-0">
                    Bienvenido/a, {{ Auth::user()->NOMBRE }}
                </h3>
            </div>
        </div>

        {{-- Mensaje de éxito --}}
        @if (session()->has('success'))
            <div id="success-message" data-flash-success class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if (session()->has('error'))
            <div id="error-message" data-flash-error class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Sección de alerta --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
            <div class="bg-[#77BF43] text-white p-4 px-4 sm:px-6">
                <h2 class="text-lg sm:text-xl font-bold m-0 uppercase">Información Importante</h2>
            </div>
            <div class="p-4 sm:p-6">
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 sm:p-5 rounded">
                    <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                        El tope máximo a solicitar por adelanto de sueldo es de <span class="font-semibold">${{ number_format($montoMaximoAdelanto, 2, ',', '.') }}</span>. La única vía de solicitud es por esta página de <span class="font-semibold">Autogestión</span>. No se puede <span class="font-semibold">modificar el monto máximo</span>. Lo que no se encuentre debidamente solicitado <span class="font-semibold">no se abonará por fuera de ninguna liquidación</span>.
                    </p>
                </div>
            </div>
        </div>

        @if($puedesolicitarAdelanto)
            {{-- FORMULARIO CUANDO ESTÁ HABILITADO --}}

            <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
                <div class="bg-[#77BF43] text-white p-4 px-4 sm:px-6">
                    <h2 class="text-lg sm:text-xl font-bold m-0 uppercase">Solicitud de Adelanto</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="mb-6">
                        <p class="text-gray-700 text-sm sm:text-base">
                            <span class="font-semibold">Fecha actual:</span> {{ $fechaActual }}
                        </p>
                    </div>

                    <div class="space-y-4 sm:space-y-5">
                        {{-- Nombre y Apellido --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                Nombre y Apellido
                            </label>
                            <input 
                                type="text" 
                                value="{{ Auth::user()->NOMBRE }}"
                                disabled
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>

                        {{-- Legajo --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                Legajo
                            </label>
                            <input 
                                type="text" 
                                value="{{ Auth::user()->LEGAJO }}"
                                disabled
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>

                        {{-- Importe solicitado --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                Importe solicitado
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm sm:text-base">$</span>
                                <input 
                                    type="number" 
                                    wire:model="montoSolicitado"
                                    max="{{ $montoMaximoAdelanto }}"
                                    min="1"
                                    step="0.01"
                                    class="w-full pl-7 sm:pl-8 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none"
                                    placeholder="Ingrese el monto">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Monto máximo: ${{ number_format($montoMaximoAdelanto, 2, ',', '.') }}
                            </p>
                        </div>

                        {{-- Forma de cobro --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                Seleccione forma de cobro
                            </label>
                            <select 
                                wire:model="formaCobro"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none">
                                <option value="">Seleccione una opción</option>
                                <option value="efectivo">Por Depósito</option>
                                <option value="cheque">Por Cheque</option>
                            </select>
                        </div>

                        {{-- Mensaje importante --}}
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 sm:p-5 rounded">
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                                <span class="font-bold text-blue-800">¡IMPORTANTE!</span> El importe solicitado <span class="font-semibold">NO necesariamente será el importe adelantado</span>, el mismo está sujeto a la disponibilidad que el agente tenga libre de descuentos, y será evaluado por RRHH. Los adelantos serán acreditados el día <span class="font-semibold">{{ $fechaAcreditacion }}</span>.
                            </p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-2">
                            <button 
                                wire:click="confirmarSolicitud"
                                class="flex-1 bg-[#77BF43] text-white px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                                Solicitar
                            </button>
                            <a 
                                href="{{ route('solicitudes') }}"
                                class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 text-center">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- MENSAJE CUANDO NO ESTÁ HABILITADO --}}

            <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-6">
                <div class="bg-[#77BF43] text-white p-4 px-4 sm:px-6">
                    <h2 class="text-lg sm:text-xl font-bold m-0 uppercase">Solicitud de Adelanto</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm sm:text-base">
                            <span class="font-semibold">Fecha actual:</span> {{ $fechaActual }}
                        </p>
                    </div>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 sm:p-5 rounded mb-6">
                        <p class="text-xs sm:text-sm text-red-700 leading-relaxed">
                            <span class="font-bold text-red-800">¡ATENCIÓN!</span> No es posible solicitar un adelanto en este momento. El período habilitado es desde el <span class="font-semibold">{{ $fechaInicial }}</span> hasta el <span class="font-semibold">{{ $fechaLimite }}</span>.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Botón Volver --}}
        <div class="flex justify-center mb-4">
            <a 
                href="{{ route('solicitudes') }}" 
                class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Solicitudes
            </a>
        </div>

    </div>
    @push('scripts')
    <script>
        function setupAutoDismiss(elementSelector, timeout = 3000) {
            const element = document.querySelector(elementSelector);
            if (element) {
                setTimeout(() => {
                    element.style.display = 'none';
                }, timeout);
            }
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            setupAutoDismiss('[data-flash-success]', 3000);
            setupAutoDismiss('[data-flash-error]', 3000);
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    setTimeout(() => {
                        const errorMessage = document.querySelector('[data-flash-error]');
                        
                        if (errorMessage) {
                            errorMessage.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                            
                            setupAutoDismiss('[data-flash-error]', 3000);
                        }
                        
                        setupAutoDismiss('[data-flash-success]', 3000);

                    }, 100);
                });
            });
        });
    </script>
    @endpush
</div>