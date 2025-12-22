<div class="pb-12 md:pb-8">
    <div class="container mx-auto px-4">

        {{-- Mensaje de error si existe --}}
        @if ($error)
            <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-2 rounded-r-lg text-sm shadow-md">
                <strong>Error:</strong> {{ $error }}
            </div>
        @endif

        @if ($recibo)
            {{-- Header del recibo con glassmorphism - DESKTOP --}}
            <div class="hidden md:block bg-[#77BF43] rounded-xl px-6 py-3 mb-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
                <div class="flex items-center justify-between">
                    {{-- Izquierda: título --}}
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                            <span class="tracking-tight">Detalle del Recibo</span>
                        </h1>
                    </div>

                    {{-- Derecha: Botón Ver PDF --}}
                    <div class="flex gap-2">
                        <a 
                            href="{{ route('recibo.pdf', [
                                'nroRecibo' => $recibo['NRO_RECIBO'],
                                'anio' => $recibo['ANIO'],
                                'mes' => $recibo['MES'],
                                'tipoLiq' => $recibo['TIPO_LIQ']
                            ]) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-700 font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-xs overflow-hidden"
                        >
                            <span class="absolute inset-0 bg-gradient-to-r from-gray-500/0 to-gray-500/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="relative z-10">Ver PDF</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Header del recibo - MOBILE --}}
            <div class="md:hidden bg-[#77BF43] rounded-xl px-4 py-3 mb-4 shadow-lg border border-white/20">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-base font-bold text-white">Detalle del Recibo</h1>
                    <a 
                        href="{{ route('recibo.pdf', [
                            'nroRecibo' => $recibo['NRO_RECIBO'],
                            'anio' => $recibo['ANIO'],
                            'mes' => $recibo['MES'],
                            'tipoLiq' => $recibo['TIPO_LIQ']
                        ]) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/90 text-gray-700 font-semibold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 shadow-md active:scale-95 transition-transform"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Ver PDF</span>
                    </a>
                </div>
                <div class="text-white/90 text-xs">
                    <p>Recibo N° <span class="font-bold">{{ $recibo['NRO_RECIBO'] }}</span></p>
                    <p>Período: <span class="font-bold">{{ $recibo['MES'] }}/{{ $recibo['ANIO'] }}</span></p>
                </div>
            </div>

            {{-- VISTA DESKTOP: Grid de tablas 2x2 --}}
            <div class="hidden md:grid grid-cols-1 lg:grid-cols-2 gap-3 mb-6 mt-6">
                
                {{-- TABLA 1: Datos Personales --}}
                <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.02] transition-all duration-300">
                    <div class="bg-[#77BF43] px-4 py-2 border-b border-white/30">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            Datos Personales
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-[#77BF43] text-white uppercase font-bold">
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
                    <div class="bg-[#77BF43] px-4 py-2 border-b border-white/30">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            Datos Laborales
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-[#77BF43] text-white uppercase font-bold">
                                <tr>
                                    <th class="px-2 py-2 text-left text-[10px]">Cargo</th>
                                    <th class="px-2 py-2 text-left text-[10px]">F. Ingreso</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Categoría</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Planta</th>
                                    <th class="px-2 py-2 text-left text-[10px]">Jurisdicción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reciboVisualizacion as $persona)
                                    <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                        <td class="px-2 py-2 text-gray-600">{{ $persona['DES_CATEGORIA'] }}</td>
                                        <td class="px-2 py-2 text-gray-600">
                                            {{ date('d / m / Y', strtotime($persona['FECH_ANTIG'])) }}
                                        </td>
                                        <td class="px-2 py-2 text-gray-600">{{ $persona['COD_CATEGORIA'] }}</td>
                                        <td class="px-2 py-2 text-gray-600">{{ $persona['DES_TIPO_PLANTA'] }}</td>
                                        <td class="px-2 py-2 text-gray-600">{{ $persona['JURISDICCION'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- VISTA MOBILE: Tarjetas de Datos Personales y Laborales --}}
            <div class="md:hidden space-y-3 mb-4">
                @foreach($reciboVisualizacion as $persona)
                    {{-- Tarjeta: Datos Personales --}}
                    <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl border border-white/50 overflow-hidden">
                        <div class="bg-[#77BF43] px-4 py-2">
                            <h2 class="text-xs font-bold text-white uppercase flex items-center gap-2">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                Datos Personales
                            </h2>
                        </div>
                        <div class="px-4 py-3 space-y-2">
                            <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">Nombre:</span>
                                <span class="text-xs text-gray-700 font-semibold text-right max-w-[60%]">{{ $persona['APELLIDO'] }}, {{ $persona['NOMBRES'] }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">Legajo:</span>
                                <span class="text-xs text-gray-700 font-semibold">{{ $recibo['LEGAJO'] }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">DNI:</span>
                                <span class="text-xs text-gray-700 font-semibold">{{ $persona['NRO_DOC'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-medium">CUIL:</span>
                                <span class="text-xs text-gray-700 font-semibold">{{ $persona['NRO_CUIT'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta: Datos Laborales --}}
                    <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl border border-white/50 overflow-hidden">
                        <div class="bg-[#77BF43] px-4 py-2">
                            <h2 class="text-xs font-bold text-white uppercase flex items-center gap-2">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                Datos Laborales
                            </h2>
                        </div>
                        <div class="px-4 py-3 space-y-2">
                            <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">Cargo:</span>
                                <span class="text-xs text-gray-700 font-semibold text-right max-w-[60%]">{{ $persona['DES_CATEGORIA'] }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">F. Ingreso:</span>
                                <span class="text-xs text-gray-700 font-semibold">{{ date('d/m/Y', strtotime($persona['FECH_ANTIG'])) }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">Categoría:</span>
                                <span class="text-xs text-gray-700 font-semibold">{{ $persona['COD_CATEGORIA'] }}</span>
                            </div>
                            <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 font-medium">Planta:</span>
                                <span class="text-xs text-gray-700 font-semibold text-right max-w-[60%]">{{ $persona['DES_TIPO_PLANTA'] }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-xs text-gray-500 font-medium">Jurisdicción:</span>
                                <span class="text-xs text-gray-700 font-semibold text-right max-w-[60%]">{{ $persona['JURISDICCION'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TABLA 3: Detalle de Conceptos (ancho completo) - DESKTOP --}}
            <div class="hidden md:block mb-8 bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl mb-3 transform hover:scale-[1.01] transition-all duration-300">
                <div class="bg-[#77BF43] px-4 py-2 border-b border-white/30">
                    <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        Detalle de Conceptos
                    </h2>
                </div>
                <div class="overflow-x-auto max-h-screen overflow-y-auto scrollbar-thin scrollbar-thumb-[#77BF43] scrollbar-track-gray-100">
                    <table class="w-full text-xs">
                        <thead class="bg-[#77BF43] text-white uppercase font-bold sticky top-0 z-10">
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

            {{-- VISTA MOBILE: Tarjetas de Conceptos --}}
            <div class="md:hidden mb-4">
                <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl border border-white/50 overflow-hidden">
                    <div class="bg-[#77BF43] px-4 py-2">
                        <h2 class="text-xs font-bold text-white uppercase flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            Detalle de Conceptos
                        </h2>
                    </div>
                    
                    @if(count($conceptos) > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($conceptos as $index => $concepto)
                                <div class="px-4 py-3 space-y-2">
                                    {{-- Encabezado del concepto --}}
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-[#77BF43]/10 text-[#77BF43] font-bold px-2 py-0.5 rounded text-xs">
                                                #{{ $index + 1 }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-medium">Cód: {{ $concepto['CONCEPTO'] ?? '-' }}</span>
                                        </div>
                                        @if($concepto['CANTIDAD'] && $concepto['CANTIDAD'] != '-')
                                            <span class="text-xs text-gray-500">Cant: {{ $concepto['CANTIDAD'] }}</span>
                                        @endif
                                    </div>

                                    {{-- Descripción del concepto --}}
                                    <div class="mb-2">
                                        <p class="text-xs text-gray-700 font-semibold">{{ $concepto['DESC_CONCEPTO'] ?? '-' }}</p>
                                    </div>

                                    {{-- Montos --}}
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        @if($concepto['MONTO'] > 0)
                                            <span class="text-xs text-gray-900 font-medium">Haber:</span>
                                            <span class="text-sm font-bold text-green-600">
                                                ${{ number_format($concepto['MONTO'], 2, ',', '.') }}
                                            </span>
                                        @elseif($concepto['MONTO'] < 0)
                                            <span class="text-xs text-gray-900 font-medium">Descuento:</span>
                                            <span class="text-sm font-bold text-red-600">
                                                ${{ number_format($concepto['MONTO'], 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sin monto</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-6 text-center">
                            <i class="fa fa-inbox text-gray-300 text-3xl mb-2"></i>
                            <p class="text-gray-400 text-xs italic">No hay conceptos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TABLA 4: Resumen de Liquidación - DESKTOP --}}
            <div class="hidden md:block mb-8 bg-gradient-to-br from-white via-white to-[#77BF43]/5 backdrop-blur-md shadow-2xl overflow-hidden border-2 border-[#77BF43]/30 rounded-xl transform hover:scale-[1.01] transition-all duration-300">
                <div class="bg-[#77BF43] px-4 py-2 border-b border-white/30">
                    <h2 class="text-xs font-bold text-white uppercase tracking-wide flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        Resumen de Liquidación
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-[#77BF43] text-white uppercase font-bold">
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
                                <td class="px-3 py-3 text-gray-600 font-semibold">${{ number_format($recibo['REMUN_C_AP'], 2, ',', '.') }}</td>
                                <td class="px-3 py-3 text-gray-600 font-semibold">${{ number_format($recibo['REMUN_S_AP'], 2, ',', '.') }}</td>
                                <td class="px-3 py-3 text-gray-600 font-semibold">${{ number_format($recibo['RETENCIONES'], 2, ',', '.') }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center gap-2 w-full h-full">
                                        <div class="bg-[#77BF43] text-white font-black text-base px-4 py-2 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                                            ${{ number_format($recibo['LIQUIDO'], 2, ',', '.') }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- VISTA MOBILE: Tarjeta de Resumen de Liquidación --}}
            <div class="md:hidden mb-4">
                <div class="bg-gradient-to-br from-white via-white to-[#77BF43]/5 backdrop-blur-md shadow-xl overflow-hidden border-2 border-[#77BF43]/30 rounded-xl">
                    <div class="bg-[#77BF43] px-4 py-2">
                        <h2 class="text-xs font-bold text-white uppercase flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            Resumen de Liquidación
                        </h2>
                    </div>
                    <div class="px-4 py-3 space-y-3">
                        <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Tipo de Liquidación:</span>
                            <span class="text-xs text-gray-700 font-semibold text-right max-w-[60%]">{{ $recibo['TIPO_LIQ'] }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Remuneración con Aporte:</span>
                            <span class="text-xs text-gray-700 font-semibold">${{ number_format($recibo['REMUN_C_AP'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Remuneración sin Aporte:</span>
                            <span class="text-xs text-gray-700 font-semibold">${{ number_format($recibo['REMUN_S_AP'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b-2 border-[#77BF43]/20">
                            <span class="text-xs text-gray-500 font-medium">Retenciones:</span>
                            <span class="text-xs text-red-600 font-semibold">${{ number_format($recibo['RETENCIONES'], 2, ',', '.') }}</span>
                        </div>
                        <div class="pt-2">
                            <div class="bg-[#77BF43] text-white text-center rounded-xl py-3 px-4 shadow-lg">
                                <p class="text-xs mb-1 opacity-90">Líquido a Cobrar</p>
                                <p class="text-xl font-bold">${{ number_format($recibo['LIQUIDO'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botón Volver - MOBILE --}}
            <div class="md:hidden flex justify-center mb-4">
                <a 
                    href="{{ route('recibos') }}" 
                    class="w-2/3 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Recibos
                </a>
            </div>
        @endif

        {{-- Botón Volver - DESKTOP --}}
        <div class="hidden md:flex justify-center mb-4">
            <a 
                href="{{ route('recibos') }}" 
                class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Recibos
            </a>
        </div>
    </div>
</div>