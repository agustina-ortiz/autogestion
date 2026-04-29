<div class="pb-12">
    {{-- Header con nombre de usuario --}}
    <div class="mb-4 hidden md:block">
        <div class="bg-[#77BF43] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Asistencias</span>
                    </h1>
                </div>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Sección de Formularios Colapsable --}}
    <div class="mb-6" x-data="{ open: false }">
        <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg border border-white/50 overflow-hidden">
            
            {{-- Header Colapsable --}}
            <button 
                @click="open = !open"
                class="w-full px-6 py-4 flex items-center justify-between 
                    hover:bg-[#77BF43]/5 transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#77BF43]
                                rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h2 class="text-[#77BF43] text-lg sm:text-xl font-bold uppercase">
                            Formularios Disponibles
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Click para ver los formularios para descargar
                        </p>
                    </div>
                </div>
                
                {{-- Ícono de flecha --}}
                <svg class="w-6 h-6 text-[#77BF43] transition-transform duration-300"
                    :class="{ 'rotate-180': open }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Contenido Colapsable --}}
            <div x-show="open" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="border-t border-gray-200"
                style="display: none;">
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        {{-- Formulario 1 --}}
                        <a href="{{ asset('formularios/Articulos.pdf') }}" 
                        target="_blank"
                        class="bg-white p-4 sm:p-5 rounded-xl shadow-md border border-gray-100
                                hover:shadow-xl hover:scale-[1.02] hover:border-[#77BF43]/30
                                transition-all duration-300 flex items-center gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#77BF43]
                                        rounded-lg flex items-center justify-center 
                                        group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">
                                    Artículo 95, inciso c)
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Descargar PDF
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-[#77BF43] flex-shrink-0 group-hover:translate-x-1 transition-transform" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        {{-- Formulario 2 --}}
                        <a href="{{ asset('formularios/Compensatorios.pdf') }}" 
                        target="_blank"
                        class="bg-white p-4 sm:p-5 rounded-xl shadow-md border border-gray-100
                                hover:shadow-xl hover:scale-[1.02] hover:border-[#91D5E2]/30
                                transition-all duration-300 flex items-center gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#77BF43]
                                        rounded-lg flex items-center justify-center 
                                        group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">
                                    Compensatorios
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Descargar PDF
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-[#91D5E2] flex-shrink-0 group-hover:translate-x-1 transition-transform" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        {{-- Formulario 3 --}}
                        <a href="{{ asset('formularios/Planilla de vacaciones.pdf') }}" 
                        target="_blank"
                        class="bg-white p-4 sm:p-5 rounded-xl shadow-md border border-gray-100
                                hover:shadow-xl hover:scale-[1.02] hover:border-[#BED630]/30
                                transition-all duration-300 flex items-center gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#77BF43]
                                        rounded-lg flex items-center justify-center 
                                        group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base truncate">
                                    Planilla de Vacaciones
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Descargar PDF
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-[#BED630] flex-shrink-0 group-hover:translate-x-1 transition-transform" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros de fecha --}}
    <div class="mb-6">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)]">
            
            <div class="flex items-end gap-2 sm:gap-3">
                
                {{-- Fecha Desde --}}
                <div class="flex-1 min-w-0 sm:flex-initial sm:w-48">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        Fecha Desde
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="fechaDesde"
                        class="w-full px-2 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg 
                            focus:ring-2 focus:ring-[#77BF43] focus:border-transparent
                            transition-all duration-200 text-gray-700 text-xs sm:text-base
                            hover:border-gray-400 cursor-pointer" />
                </div>

                {{-- Fecha Hasta --}}
                <div class="flex-1 min-w-0 sm:flex-initial sm:w-48">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        Fecha Hasta
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="fechaHasta"
                        class="w-full px-2 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg 
                            focus:ring-2 focus:ring-[#77BF43] focus:border-transparent
                            transition-all duration-200 text-gray-700 text-xs sm:text-base
                            hover:border-gray-400 cursor-pointer" />
                </div>

                {{-- Botón Limpiar --}}
                <div class="flex-shrink-0">
                    <button 
                        wire:click="limpiarFiltros"
                        class="px-3 sm:px-6 py-2 sm:py-2.5 
                            bg-[#77BF43]
                            text-white font-semibold rounded-lg text-xs sm:text-base
                            transition-all duration-300 
                            hover:shadow-lg hover:scale-[1.02]
                            active:scale-[0.98]
                            shadow-[0_2px_4px_rgba(119,191,67,0.3)]
                            whitespace-nowrap">
                            Limpiar
                        </button>
                </div>

            </div>

        </div>
    </div>

    {{-- Últimas 3 Fichadas --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-2 h-5 bg-[#77BF43] rounded-full"></div>
            <h2 class="text-[#77BF43] text-sm font-bold uppercase tracking-wide">Últimas Fichadas</h2>
        </div>

        @if(count($ultimasFichadas) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($ultimasFichadas as $i => $uf)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3
                                border-l-4 border-l-[#BED630]">
                        
                        {{-- Ícono --}}
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center bg-[#BED630]/30">
                            <svg class="w-4 h-4 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        {{-- Datos --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">
                                {{ $i === 0 ? 'Última' : ($i === 1 ? 'Anterior' : 'Hace 2') }}
                            </p>
                            <p class="text-sm font-bold text-gray-800 leading-tight">
                                {{ \Carbon\Carbon::parse($uf->fecha)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 font-mono">
                                {{ $uf->hora }}
                            </p>
                        </div>

                        {{-- Badge --}}
                        <div class="flex-shrink-0">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#77BF43]/10 text-[#77BF43]">
                                Fichada
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-sm text-gray-400 text-center">
                No se encontraron fichadas registradas
            </div>
        @endif
    </div>

    <!-- Tabla de Fichadas -->
    <h2 class="text-[#77BF43] text-xl font-bold mb-4 uppercase hidden md:block">Fichadas</h2>
    <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.01] transition-all duration-300 mb-6">
        
        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#77BF43] text-white uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center text-[10px]">#</th>
                        <th class="px-3 py-2 text-center text-[10px]">Fecha</th>
                        <th class="px-3 py-2 text-center text-[10px]">Hora</th>
                        <th class="px-3 py-2 text-center text-[10px]">Observaciones</th>
                        <th class="px-3 py-2 text-center text-[10px]">Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($fichadas) > 0)
                        @php $i = $offset + 1; @endphp
                        @foreach($fichadas as $fichada)
                            <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                <td class="px-3 py-2 text-center font-semibold text-gray-700">{{ $i }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ \Carbon\Carbon::parse($fichada->fecha)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">
                                    @if($fichada->tipo == 'F')
                                        {{ $fichada->hora }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center text-gray-600">
                                    @if($fichada->tipo == 'F')
                                        Fichada
                                    @else
                                        {{ $fichada->hora }}
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if(trim($fichada->certifi) != '')
                                    <div class="w-7 h-7 bg-[#77BF43] rounded-lg flex items-center justify-center mx-auto">
                                        <a href="{{ $fichada->certifi }}" target="_blank" class="text-blue-600 hover:underline">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @php $i++; @endphp
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No hay fichadas para mostrar en el período seleccionado
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($totalPages > 1)
            <div class="bg-gray-50/80 px-6 py-3 border-t border-gray-200/70">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-600">
                        Mostrando {{ $offset + 1 }} - {{ min($offset + $perPage, $totalRecords) }} de {{ $totalRecords }} resultados
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        {{-- Botón Primera Página --}}
                        @if ($currentPage > 1)
                            <button 
                                wire:click="gotoPage(1)"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center gap-1 shadow-sm hover:shadow-md">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Primero</span>
                            </button>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Primero</span>
                            </span>
                        @endif

                        {{-- Botón Anterior --}}
                        @if ($currentPage > 1)
                            <button 
                                wire:click="previousPage"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center gap-1 shadow-sm hover:shadow-md">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Anterior</span>
                            </button>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Anterior</span>
                            </span>
                        @endif

                        {{-- Números de página: 2 anteriores, actual, 2 siguientes --}}
                        @php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                        @endphp

                        @for ($i = $startPage; $i <= $endPage; $i++)
                            @if ($i == $currentPage)
                                <button 
                                    class="px-3 py-1.5 rounded-lg transition-all duration-200 text-xs font-bold bg-[#77BF43] text-white border border-[#77BF43] shadow-md">
                                    {{ $i }}
                                </button>
                            @else
                                <button 
                                    wire:click="gotoPage({{ $i }})"
                                    class="px-3 py-1.5 border border-gray-300 rounded-lg transition-all duration-200 text-xs font-medium bg-white text-gray-700 hover:bg-gray-50 shadow-sm hover:shadow-md">
                                    {{ $i }}
                                </button>
                            @endif
                        @endfor

                        {{-- Botón Siguiente --}}
                        @if ($currentPage < $totalPages)
                            <button 
                                wire:click="nextPage"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center gap-1 shadow-sm hover:shadow-md">
                                <span class="hidden sm:inline">Siguiente</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                <span class="hidden sm:inline">Siguiente</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif

                        {{-- Botón Última Página --}}
                        @if ($currentPage < $totalPages)
                            <button 
                                wire:click="gotoPage({{ $totalPages }})"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center gap-1 shadow-sm hover:shadow-md">
                                <span class="hidden sm:inline">Último</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                <span class="hidden sm:inline">Último</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Tabla de Novedades -->
    <h2 class="text-[#77BF43] text-xl font-bold mb-4 uppercase hidden md:block">
        @if(count($novedades) > 0)
            Novedades {{ $year }}
        @else
            Novedades
        @endif
    </h2>

    <!-- Título mobile -->
    <h2 class="text-[#77BF43] text-lg font-bold mb-3 uppercase md:hidden">
        @if(count($novedades) > 0)
            Novedades {{ $year }}
        @else
            Novedades
        @endif
    </h2>

    <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.01] transition-all duration-300 mb-6">
        @if(count($novedades) > 0)
            <!-- Contenedor con scroll horizontal -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-max">
                    <thead class="bg-[#77BF43] text-white uppercase font-bold">
                        <tr>
                            <th class="px-3 py-2 text-center text-[10px] sticky left-0 bg-[#77BF43] z-10">Código</th>
                            <th class="px-3 py-2 text-left text-[10px] sticky left-[60px] bg-[#77BF43] z-10 min-w-[120px]">Nombre</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Ene</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Feb</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Mar</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Abr</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">May</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Jun</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Jul</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Ago</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Sep</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Oct</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Nov</th>
                            <th class="px-3 py-2 text-center text-[10px] min-w-[50px]">Dic</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($novedades as $novedad)
                            <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                <td class="px-3 py-2 text-center font-semibold text-gray-700 sticky left-0 bg-white z-[5]">{{ $novedad->codigo }}</td>
                                <td class="px-3 py-2 text-left text-gray-700 font-medium sticky left-[60px] bg-white z-[5]">{{ $novedad->nombre }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->ene }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->feb }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->mar }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->abr }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->may }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->jun }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->jul }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->ago }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->sep }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->oct }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->nov }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $novedad->dic }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            
        @else
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                No hay novedades para mostrar en el período seleccionado
            </div>
        @endif
    </div>
    
    {{-- Botón Volver --}}
    <div class="hidden md:flex justify-center mt-10">
        <a 
            href="{{ route('dashboard') }}" 
            class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Inicio
        </a>
    </div>
</div>