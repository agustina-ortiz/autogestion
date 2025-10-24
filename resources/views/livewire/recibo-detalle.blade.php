<div class="min-h-screen py-4">
    <div class="container mx-auto px-4 max-w-7xl">

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
                    {{-- Izquierda: Icono de volver + título --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('recibos') }}" 
                        class="text-white hover:text-white/80 transition-transform transform hover:-translate-x-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        {{-- Título con efecto de brillo --}}
                        <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                            
                            <span class="tracking-tight">Detalle del Recibo</span>
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

            {{-- Grid de tablas 2x2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-6 mt-6">
                
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
                                @foreach($reciboVisualizacion as $persona)
                                <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                    <td class="px-2 py-2 font-bold text-[#77BF43]">{{ $recibo['NRO_RECIBO'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">{{ $persona['APELLIDO'] }}, {{ $persona['NOMBRES'] }}</td>
                                    <td class="px-2 py-2 text-gray-800 font-semibold">{{ $recibo['LEGAJO'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">{{ $persona['NRO_DOC'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">{{ $persona['NRO_CUIT'] }}</td>
                                    <td class="px-2 py-2 text-gray-800 font-semibold">{{ $recibo['MES'] }}/{{ $recibo['ANIO'] }}</td>
                                </tr>
                                @endforeach
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
                                    <td class="px-2 py-2 text-gray-600">
                                        {{ date('d / m / Y', strtotime($persona['FECH_ANTIG'])) }}
                                    </td>
                                    <td class="px-2 py-2 text-gray-600">{{ $recibo['ITEM'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">{{ $persona['DES_TIPO_PLANTA'] }}</td>
                                    <td class="px-2 py-2 text-gray-600">{{ $persona['JURISDICCION'] }}</td>
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
                <div class="overflow-x-auto max-h-screen overflow-y-auto scrollbar-thin scrollbar-thumb-[#77BF43] scrollbar-track-gray-100">
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
                                        <td class="px-2 py-2 text-left text-gray-600">{{ $concepto['DESC_CONCEPTO'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-right text-gray-600">{{ $concepto['MONTO'] > 0 ? '$'.number_format($concepto['MONTO'],2,',','.') : '-' }}</td>
                                        <td class="px-2 py-2 text-right text-gray-600">{{ $concepto['MONTO'] < 0 ? '$'.number_format($concepto['MONTO'],2,',','.') : '-' }}</td>
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
                                <th class="px-3 py-2 text-left text-[10px]">Tipo de Liquidación</th>
                                <th class="px-3 py-2 text-left text-[10px]">Remuneración con Aporte</th>
                                <th class="px-3 py-2 text-left text-[10px]">Remuneración sin Aporte</th>
                                <th class="px-3 py-2 text-left text-[10px]">Retenciones</th>
                                <th class="px-3 py-2 text-center text-[10px]">Líquido a Cobrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-[#77BF43]/5 transition-colors duration-200">
                                <td class="px-3 py-3 text-gray-600 font-semibold">{{ $recibo['TIPO_LIQ'] }}</td>
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

    <!-- Modal de impresión del recibo -->
    <div 
        x-data="{ open: false, cerrarDespuesDeImprimir() { window.onafterprint = () => { this.open = false; } } }"
        x-init="cerrarDespuesDeImprimir()"
        x-on:abrir-modal-impresion-recibo.window="open = true; setTimeout(() => window.print(), 500);"
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-4xl mx-4 print:w-full print:h-auto print:shadow-none print:p-4 text-xs">
            @if($recibo)
            <div class="print-area">

                <!-- Header -->
                <div class="flex justify-between items-center border-b pb-2 mb-8 text-sm">
                    <h1 class="text-lg font-bold text-gray-800">Detalle del Recibo</h1>
                    <p class="text-gray-600 mr-8">Tipo de Liquidación: {{ $recibo['TIPO_LIQ'] }} - Período: {{ $recibo['MES'] }}/{{ $recibo['ANIO'] }}</p>
                </div>

                <!-- Datos Personales y Laborales lado a lado -->
                <div class="flex justify-between mb-4 text-sm gap-4">
                    <!-- Datos Personales -->
                    <div class="w-1/2">
                        <h2 class="font-semibold mb-2 border-b pb-1">Datos Personales</h2>
                        <p><strong>Nombre:</strong> {{ $reciboVisualizacion[0]['APELLIDO'] }}, {{ $reciboVisualizacion[0]['NOMBRES'] }}</p>
                        <p><strong>Legajo:</strong> {{ $recibo['LEGAJO'] }}</p>
                        <p><strong>DNI:</strong> {{ $reciboVisualizacion[0]['NRO_DOC'] }}</p>
                        <p><strong>CUIL:</strong> {{ $reciboVisualizacion[0]['NRO_CUIT'] }}</p>
                    </div>

                    <!-- Datos Laborales -->
                    <div class="w-1/2">
                        <h2 class="font-semibold mb-2 border-b pb-1">Datos Laborales</h2>
                        <p><strong>Cargo:</strong> {{ $recibo['NRO_CARGO'] }}</p>
                        <p><strong>Fecha Ingreso:</strong> {{ date('d/m/Y', strtotime($reciboVisualizacion[0]['FECH_ANTIG'])) }}</p>
                        <p><strong>Categoría:</strong> {{ $recibo['ITEM'] }}</p>
                        <p><strong>Planta:</strong> {{ $reciboVisualizacion[0]['DES_TIPO_PLANTA'] }}</p>
                        <p><strong>Jurisdicción:</strong> {{ $reciboVisualizacion[0]['JURISDICCION'] }}</p>
                    </div>
                </div>

                <!-- Detalle de conceptos -->
                <div class="mb-4 text-xs">
                    <h2 class="font-semibold mb-2 border-b pb-1">Detalle de Conceptos</h2>
                    <table class="w-full border border-gray-300 text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-1 border text-center">Cant.</th>
                                <th class="p-1 border text-center">Código</th>
                                <th class="p-1 border text-left">Concepto</th>
                                <th class="p-1 border text-right">Haberes</th>
                                <th class="p-1 border text-right">Descuento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($conceptos as $concepto)
                            <tr>
                                <td class="p-1 border text-center">{{ $concepto['CANTIDAD'] ?? '-' }}</td>
                                <td class="p-1 border text-center">{{ $concepto['CONCEPTO'] ?? '-' }}</td>
                                <td class="p-1 border text-left">{{ $concepto['DESC_CONCEPTO'] ?? '-' }}</td>
                                <td class="p-1 border text-right">{{ $concepto['MONTO'] > 0 ? '$'.number_format($concepto['MONTO'],2,',','.') : '-' }}</td>
                                <td class="p-1 border text-right">{{ $concepto['MONTO'] < 0 ? '$'.number_format($concepto['MONTO'],2,',','.') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Resumen -->
                <div class="text-right text-sm">
                    <p class="font-bold mr-32">Líquido a Cobrar: ${{ number_format($recibo['LIQUIDO'],2,',','.') }}</p>
                </div>

            </div>
            @endif
        </div>

        <style>
            @media print {
                body * { visibility: hidden; }
                .print-area, .print-area * { visibility: visible; }
                .print-area { position: absolute; left: 0; top: 0; width: 210mm; padding: 15mm; background: white; }
                @page { size: A4; margin: 10mm; }
            }
        </style>
    </div>
</div>