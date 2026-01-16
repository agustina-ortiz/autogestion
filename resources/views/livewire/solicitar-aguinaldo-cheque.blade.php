<div class="p-4 sm:p-0">
    {{-- Header con nombre de usuario --}}
    <div class="mb-4 hidden md:block">
        <div class="bg-[#77BF43] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Solicitar Aguinaldo por Cheque</span>
                    </h1>
                </div>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Mensaje de error --}}
    @if (session()->has('error'))
        <div id="error-message" data-flash-error class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulario de solicitud --}}
    <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-8">
        <div class="bg-[#77BF43] text-white p-4 px-4 sm:px-6">
            <h2 class="text-lg sm:text-xl font-bold m-0 uppercase">Solicitud de Aguinaldo por CHEQUE</h2>
        </div>
        <div class="p-4 sm:p-6">
            {{-- Fecha actual --}}
            <div class="mb-6">
                <p class="text-gray-700 text-sm sm:text-base">
                    <span class="font-semibold">Fecha actual:</span> {{ $fechaActual }}
                </p>
            </div>

            {{-- Información importante --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 sm:p-4 mb-6 rounded">
                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                    Las solicitudes de sueldo por cheque se realizarán <span class="font-semibold">únicamente por Autogestión</span>.
                </p>
            </div>

            {{-- Información del solicitante --}}
            <div class="space-y-5 sm:space-y-6">
                {{-- Apellido y Nombre --}}
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                        Apellido y Nombre
                    </label>
                    <input 
                        type="text" 
                        value="{{ $nombreCompleto }}"
                        readonly
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                        disabled>
                </div>

                {{-- Legajo --}}
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                        Legajo
                    </label>
                    <input 
                        type="text" 
                        value="{{ $legajo }}"
                        readonly
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                        disabled>
                </div>

                {{-- Forma de cobro (fijo en cheque) --}}
                <div class="mb-6">
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                        Forma de Cobro
                    </label>
                    <select 
                        disabled
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] outline-none">
                        <option value="cheque">Por Cheque</option>
                    </select>
                </div>
                
                <div class="flex justify-center">
                    <button 
                        wire:click="confirmarSolicitud"
                        class="w-full sm:w-1/2 bg-[#77BF43] text-white px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                        Confirmar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Botón de Volver --}}
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
            setupAutoDismiss('[data-flash-error]', 3000);
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('scroll-to-error', () => {
                setTimeout(() => {
                    const errorMessage = document.querySelector('[data-flash-error]');
                    if (errorMessage) {
                        errorMessage.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        setupAutoDismiss('[data-flash-error]', 3000);
                    }
                }, 100);
            });
        });
    </script>
    @endpush
</div>