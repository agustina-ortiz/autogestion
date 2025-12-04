<div class="pb-8">
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

    <!-- Filtros de fecha -->
    <div class="filtros-section 
                flex flex-col md:flex-row 
                items-end md:items-end 
                gap-4 md:flex-wrap bg-white p-6 rounded-xl shadow mb-4">

        <!-- Contenedor horizontal solo en mobile -->
        <div class="flex flex-row w-full md:w-auto gap-4">
            <flux:field class="flex-1">
                <flux:label>Fecha Desde</flux:label>
                <flux:input type="date" wire:model.live="fechaDesde" />
            </flux:field>

            <flux:field class="flex-1">
                <flux:label>Fecha Hasta</flux:label>
                <flux:input type="date" wire:model.live="fechaHasta" />
            </flux:field>
        </div>

        <!-- Botón centrado solo en mobile -->
        <div class="w-full flex justify-center md:w-auto md:block">
            <button 
                wire:click="limpiarFiltros"
                class="bg-[#77BF43] text-white px-6 py-2 rounded-lg font-semibold cursor-pointer 
                    transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] 
                    hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] 
                    hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                Limpiar Filtros
            </button>
        </div>

    </div>

    <!-- Tabla de Fichadas -->
    <h2 class="section-title">Fichadas</h2>
    <div class="bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.01] transition-all duration-300 mb-6">
        
        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#77BF43] text-white uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center text-[10px]">#</th>
                        <th class="px-3 py-2 text-center text-[10px]">Tarjeta</th>
                        <th class="px-3 py-2 text-center text-[10px]">Fecha y Hora</th>
                        <th class="px-3 py-2 text-center text-[10px]">Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($fichadas) > 0)
                        @php $i = $offset + 1; @endphp
                        @foreach($fichadas as $fichada)
                            <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                <td class="px-3 py-2 text-center font-semibold text-gray-700">{{ $i }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $fichada->codtar }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">
                                    @if($fichada->tipo == 'F')
                                        {{ \Carbon\Carbon::parse($fichada->fecha)->format('d/m/Y') }} {{ $fichada->hora }}
                                    @else
                                        {{ \Carbon\Carbon::parse($fichada->fecha)->format('d/m/Y') }} - {{ $fichada->hora }}
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if(trim($fichada->certifi) != '')
                                        <a href="{{ $fichada->certifi }}" target="_blank" class="text-blue-600 hover:underline">
                                            Ver certificado
                                        </a>
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
    <h2 class="section-title">
        @if(count($novedades) > 0)
            Novedades {{ $year }}
        @else
            Novedades
        @endif
    </h2>

    <div class="table-container">
        @if(count($novedades) > 0)
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Ene</th>
                        <th>Feb</th>
                        <th>Mar</th>
                        <th>Abr</th>
                        <th>May</th>
                        <th>Jun</th>
                        <th>Jul</th>
                        <th>Ago</th>
                        <th>Sep</th>
                        <th>Oct</th>
                        <th>Nov</th>
                        <th>Dic</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($novedades as $novedad)
                        <tr>
                            <td>{{ $novedad->codigo }}</td>
                            <td>{{ $novedad->nombre }}</td>
                            <td>{{ $novedad->ene }}</td>
                            <td>{{ $novedad->feb }}</td>
                            <td>{{ $novedad->mar }}</td>
                            <td>{{ $novedad->abr }}</td>
                            <td>{{ $novedad->may }}</td>
                            <td>{{ $novedad->jun }}</td>
                            <td>{{ $novedad->jul }}</td>
                            <td>{{ $novedad->ago }}</td>
                            <td>{{ $novedad->sep }}</td>
                            <td>{{ $novedad->oct }}</td>
                            <td>{{ $novedad->nov }}</td>
                            <td>{{ $novedad->dic }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay novedades para mostrar en el período seleccionado</div>
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