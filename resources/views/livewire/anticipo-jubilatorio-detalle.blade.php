<div class="">
    {{-- Header con nombre de usuario --}}
    <div class="mb-3">
        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Detalle de Anticipo Jubilatorio</span>
                    </h1>
                </div>
                {{-- Derecha: Botones PDF, Imprimir, Email --}}
                <div class="flex gap-2">
                    <button 
                        wire:click="abrirModalImpresion"
                        class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-700 font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-xs overflow-hidden"
                    >
                        <span class="absolute inset-0 bg-gradient-to-r from-gray-500/0 to-gray-500/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                        <svg class="w-4 h-4 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span class="relative z-10">Imprimir</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensaje de error si existe --}}
    @if (session()->has('error'))
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-2 rounded-r-lg text-sm shadow-md animate-pulse">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Mensaje de info si existe --}}
    @if (session()->has('info'))
        <div class="mb-3 bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-4 py-2 rounded-r-lg text-sm shadow-md">
            <strong>Info:</strong> {{ session('info') }}
        </div>
    @endif

    {{-- Datos del Empleado y Periodo --}}
    <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl mb-4">
        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2">
            <h3 class="text-white font-bold text-sm uppercase">Información del Anticipo</h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#77BF43]">Nombre:</span>
                <span class="text-gray-700">{{ $empleado->NOMBRE }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#77BF43]">Legajo:</span>
                <span class="text-gray-700">{{ $empleado->LEGAJO }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#77BF43]">Periodo:</span>
                <span class="text-gray-700">{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $anio }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#77BF43]">Liquidación:</span>
                <span class="text-gray-700">{{ $tipo }}</span>
            </div>
        </div>
    </div>

    {{-- Tabla de Conceptos --}}
    <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl mb-4">
        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2">
            <h3 class="text-white font-bold text-sm uppercase">Detalle de Liquidación</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-100 border-b-2 border-[#77BF43]">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-700">CONCEPTO</th>
                        <th class="px-3 py-2 text-center text-[10px] font-bold text-gray-700">IMPORTE</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($conceptos) > 0)
                        @foreach ($conceptos as $concepto)                            
                            @if ($concepto['bruto'] > 0)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-left text-gray-600 pl-8">Importe Bruto</td>
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        ${{ number_format($concepto['bruto'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            
                            @if ($concepto['ioma'] > 0)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-left text-gray-600 pl-8">Descuento I.O.M.A</td>
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        ${{ number_format($concepto['ioma'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            
                            @if (isset($concepto['iomaconyuge']) && $concepto['iomaconyuge'] > 0)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-left text-gray-600 pl-8">I.O.M.A Cónyuge</td>
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        ${{ number_format($concepto['iomaconyuge'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            
                            @if (isset($concepto['ctaalim']) && $concepto['ctaalim'] > 0)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-left text-gray-600 pl-8">Cuenta Alimentaria</td>
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        ${{ number_format($concepto['ctaalim'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            
                            @if (isset($concepto['ajctaalim']) && $concepto['ajctaalim'] > 0)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-left text-gray-600 pl-8">Ajuste Cuenta Alimentaria</td>
                                    <td class="px-3 py-2 text-center text-gray-600">
                                        ${{ number_format($concepto['ajctaalim'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        
                        {{-- Fila de Total --}}
                        <tr class="bg-gradient-to-r from-[#77BF43]/10 to-[#BED630]/10 border-t-2 border-[#77BF43]">
                            <td class="px-3 py-3 text-right font-bold text-gray-800 uppercase">
                                NETO A COBRAR:
                            </td>
                            <td class="px-3 py-3 text-center font-bold text-lg text-[#77BF43]">
                                ${{ number_format($netoACobrar, 2, ',', '.') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No se encontraron conceptos para este anticipo
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Botones de Acción --}}
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-6">
        {{-- Botón Volver --}}
        <a 
            href="{{ route('anticipo.jubilatorio') }}" 
            class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Anticipos
        </a>
    </div>
</div>