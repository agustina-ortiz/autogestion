<div class="min-h-screen py-4">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- Botón volver --}}
        <div class="mb-3">
            <a href="{{ route('recibos') }}" 
               class="inline-flex items-center gap-2 text-[#77BF43] hover:text-[#6BA939] font-semibold transition-all duration-300 text-sm hover:gap-3 group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a Mis Recibos
            </a>
        </div>

        {{-- Mensaje de error si existe --}}
        @if ($error)
            <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-2 rounded-r-lg text-sm shadow-md animate-pulse">
                <strong>Error:</strong> {{ $error }}
            </div>
        @endif

        @if ($recibo)
            {{-- Header del recibo con glassmorphism --}}
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] rounded-xl px-6 py-3 mb-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
                <div class="flex items-center justify-between">
                    {{-- Título con efecto de brillo --}}
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="tracking-tight">Detalle del Recibo</span>
                    </h1>

                    {{-- Botones con efectos modernos --}}
                    <div class="flex gap-2">
                        <button class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-[#77BF43] font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-xs overflow-hidden">
                            <span class="absolute inset-0 bg-gradient-to-r from-[#77BF43]/0 to-[#77BF43]/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="relative z-10">PDF</span>
                        </button>

                        <button class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-700 font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-xs overflow-hidden">
                            <span class="absolute inset-0 bg-gradient-to-r from-gray-500/0 to-gray-500/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span class="relative z-10">Imprimir</span>
                        </button>

                        <button class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-blue-600 font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-xs overflow-hidden">
                            <span class="absolute inset-0 bg-gradient-to-r from-blue-500/0 to-blue-500/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="relative z-10">Email</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Grid de tablas 2x2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-8 mt-8">
                
                {{-- TABLA 1: Datos Personales --}}
                <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.02] transition-all duration-300">
                    <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2 border-b border-white/30">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            Datos Personales
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white uppercase font-bold">
                                <tr>
                                    <th class="px-2 py-2 text-left text-[10px]">Nro Recibo</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Nombre</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Legajo</th>
                                    <th class="px-2 py-2 text-left text-[10px]">DNI</th>
                                    <th class="px-2 py-2 text-left text-[10px]">CUIL</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Período</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                    <td class="px-2 py-2 font-bold text-[#77BF43]">{{ $recibo['NRO_RECIBO'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-800 font-semibold">{{ $recibo['LEGAJO'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-800 font-semibold">{{ $recibo['MES'] }}/{{ $recibo['ANIO'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TABLA 2: Datos Laborales --}}
                <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.02] transition-all duration-300">
                    <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2 border-b border-white/30">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            Datos Laborales
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white uppercase font-bold">
                                <tr>
                                    <th class="px-2 py-2 text-left text-[10px]">Cargo</th>
                                    <th class="px-2 py-2 text-left text-[10px]">F. Ingreso</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Categoría</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Planta</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Jurisdicción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                    <td class="px-2 py-2 text-gray-600">{{ $recibo['NRO_CARGO'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                    <td class="px-2 py-2 text-gray-600">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- TABLA 3: Detalle de Conceptos (ancho completo) --}}
            <div class="mb-8 bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl mb-3 transform hover:scale-[1.01] transition-all duration-300">
                <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2 border-b border-white/30">
                    <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        Detalle de Conceptos
                    </h2>
                </div>
                <div class="overflow-x-auto max-h-32 overflow-y-auto scrollbar-thin scrollbar-thumb-[#77BF43] scrollbar-track-gray-100">
                    <table class="w-full text-xs">
                        <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white uppercase font-bold sticky top-0 z-10">
                            <tr>
                                <th class="px-2 py-2 text-center text-[10px]">Cant.</th>
                                <th class="px-2 py-2 text-center text-[10px]">Código</th>
                                <th class="px-2 py-2 text-left text-[10px]">Concepto</th>
                                <th class="px-2 py-2 text-right text-[10px]">Haberes</th>
                                <th class="px-2 py-2 text-right text-[10px]">Descuento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($conceptos) > 0)
                                @foreach($conceptos as $concepto)
                                    <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                        <td class="px-2 py-2 text-center text-gray-600">{{ $concepto['CANTIDAD'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-center text-gray-600">{{ $concepto['CONCEPTO'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-left text-gray-600">{{ $concepto['DESCRIPCION'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-right text-gray-600">{{ isset($concepto['HABERES']) ? '$'.number_format($concepto['HABERES'],2,',','.') : '-' }}</td>
                                        <td class="px-2 py-2 text-right text-gray-600">{{ isset($concepto['DESCUENTOS']) ? '$'.number_format($concepto['DESCUENTOS'],2,',','.') : '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-2 py-3 text-center text-gray-400 italic text-[10px]">
                                        No hay conceptos disponibles
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                    </table>
                </div>
            </div>

            {{-- TABLA 4: Resumen de Liquidación con efecto destacado --}}
            <div class="bg-gradient-to-br from-white via-white to-[#77BF43]/5 backdrop-blur-md shadow-2xl overflow-hidden border-2 border-[#77BF43]/30 rounded-xl transform hover:scale-[1.01] transition-all duration-300">
                <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-4 py-2 border-b border-white/30">
                    <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        Resumen de Liquidación
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white uppercase font-bold">
                            <tr>
                                <th class="px-3 py-2 text-left text-[10px]">Remuneración con Aporte</th>
                                <th class="px-3 py-2 text-left text-[10px]">Remuneración sin Aporte</th>
                                <th class="px-3 py-2 text-left text-[10px]">Retenciones</th>
                                <th class="px-3 py-2 text-center text-[10px]">Líquido a Cobrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-[#77BF43]/5 transition-colors duration-200">
                                <td class="px-3 py-3 text-gray-600 font-semibold">{{ $recibo['REMUN_C_AP'] }}</td>
                                <td class="px-3 py-3 text-gray-600 font-semibold">{{ $recibo['REMUN_S_AP'] }}</td>
                                <td class="px-3 py-3 text-gray-600 font-semibold">{{ $recibo['RETENCIONES'] }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center gap-2 w-full h-full">
                                        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white font-black text-base px-4 py-2 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                                            ${{ number_format($recibo['LIQUIDO'], 2, ',', '.') }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        @endif
    </div>
</div>