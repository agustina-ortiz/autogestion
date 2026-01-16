<div class="pb-16 md:pb-2" x-data="{ 
    isMobile: window.innerWidth < 768,
    init() {
        this.updatePerPage();
        window.addEventListener('resize', () => {
            const wasMobile = this.isMobile;
            this.isMobile = window.innerWidth < 768;
            if (wasMobile !== this.isMobile) {
                this.updatePerPage();
            }
        });
    },
    updatePerPage() {
        $wire.updatePerPage(this.isMobile);
    }
}">
    {{-- Header con nombre de usuario --}}
    <div class="mb-3 hidden md:block">
        <div class="bg-[#77BF43] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Mis Recibos</span>
                    </h1>
                </div>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Mensaje de error si existe --}}
    @if (session()->has('error'))
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-2 rounded-r-lg text-sm shadow-md animate-pulse">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Header - Visible en móvil y escritorio --}}
    <div class="flex flex-row justify-between mb-4">
        <h2 class="text-[#77BF43] text-xl font-bold uppercase">
            Recibos
        </h2>
    </div>

    {{-- Vista de TABLA para ESCRITORIO --}}
    <div class="hidden md:block bg-white/90 backdrop-blur-md shadow-xl overflow-hidden border border-white/50 rounded-xl transform hover:scale-[1.01] transition-all duration-300">
        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#77BF43] text-white uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center text-[10px]">#</th>
                        <th class="px-3 py-2 text-center text-[10px]">Año</th>
                        <th class="px-3 py-2 text-center text-[10px]">Mes</th>
                        <th class="px-3 py-2 text-left text-[10px]">Tipo de Liquidación</th>
                        <th class="px-3 py-2 text-center text-[10px]">Recibo N°</th>
                        <th class="px-3 py-2 text-center text-[10px]">Importe Neto</th>
                        <th class="px-3 py-2 text-center text-[10px]">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($rows) > 0)
                        @php $i = $offset + 1; @endphp
                        @foreach ($rows as $row)
                            <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-200 border-t border-gray-100">
                                <td class="px-3 py-2 text-center font-semibold text-gray-700">{{ $i }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $row['ANIO'] }}</td>
                                <td class="px-3 py-2 text-center text-gray-600">{{ $row['MES'] }}</td>
                                <td class="px-3 py-2 text-left text-gray-600">{{ $row['TIPO_LIQ'] }}</td>
                                <td class="px-3 py-2 text-center font-bold text-[#77BF43]">{{ $row['NRO_RECIBO'] }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="font-bold text-gray-800">
                                        ${{ number_format($row['LIQUIDO'], 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <a href="{{ route('recibo', ['numero' => $row['NRO_RECIBO'], 'anio' => $row['ANIO'], 'mes' => $row['MES'], 'tipo' => $row['TIPO_LIQ']]) }}"
                                    class="group relative inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-700 font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 text-[10px] overflow-hidden">
                                        <span class="absolute inset-0 bg-gradient-to-r from-[#77BF43]/0 to-[#77BF43]/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500"></span>
                                        <i class="fa fa-eye relative z-10"></i>
                                        <span class="relative z-10">VER</span>
                                    </a>
                                </td>
                            </tr>
                            @php $i++; @endphp
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No hay recibos disponibles
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Paginación ESCRITORIO --}}
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

    {{-- Vista de TARJETAS para MÓVIL --}}
    <div class="md:hidden space-y-3">
        @if (count($rows) > 0)
            @php $i = $offset + 1; @endphp
            @foreach ($rows as $row)
            <a href="{{ route('recibo', ['numero' => $row['NRO_RECIBO'], 'anio' => $row['ANIO'], 'mes' => $row['MES'], 'tipo' => $row['TIPO_LIQ']]) }}"
            class="block">
                <div class="bg-white/90 shadow-md rounded-xl border-2 border-gray-100 overflow-hidden transform hover:scale-[1.02] transition-all duration-300 hover:border-[#77BF43]/30 hover:shadow-lg">

                    {{-- Contenido de la tarjeta --}}
                    <div class="px-4 py-3 space-y-2">
                        {{-- Tipo de Liquidación --}}
                        <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                            <span class="text-base font-bold" style="color: {{ $row['COLOR_TIPO'] }}">
                                <span class="text-xs text-gray-500 font-medium">Recibo N°:</span> {{ $row['NRO_RECIBO'] }}
                            </span>
                            <span class="text-base font-bold" style="color: {{ $row['COLOR_TIPO'] }}"><span class="text-xs text-gray-500 font-medium">
                                </span> {{ $row['MES'] }}/{{ $row['ANIO'] }}
                            </span>
                        </div>
                        
                        {{-- Importe Neto --}}
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold" style="color: {{ $row['COLOR_TIPO'] }}">
                                <span class="text-xs text-gray-500 font-medium">Tipo:</span> {{ $row['TIPO_LIQ'] }}
                            </span>

                            <span class="text-base font-bold" style="color: {{ $row['COLOR_TIPO'] }}">
                                ${{ number_format($row['LIQUIDO'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
                @php $i++; @endphp
            @endforeach
        @else
            <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl border border-white/50 px-6 py-8 text-center">
                <i class="fa fa-inbox text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500 text-sm">No hay recibos disponibles</p>
            </div>
        @endif

        {{-- Paginación MÓVIL --}}
        @if ($totalPages > 1)
            <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl border border-white/50 px-4 py-3 mt-4">
                <div class="flex flex-col items-center gap-3">
                    {{-- Info de resultados --}}
                    <div class="text-xs text-gray-600 text-center">
                        Mostrando {{ $offset + 1 }} - {{ min($offset + $perPage, $totalRecords) }} de {{ $totalRecords }}
                    </div>
                    
                    {{-- Botones de navegación --}}
                    <div class="flex items-center justify-center gap-2 w-full">
                        {{-- Botón Anterior --}}
                        @if ($currentPage > 1)
                            <button 
                                wire:click="previousPage"
                                class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center justify-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span>Anterior</span>
                            </button>
                        @else
                            <span class="flex-1 px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span>Anterior</span>
                            </span>
                        @endif

                        {{-- Indicador de página actual --}}
                        <div class="px-4 py-2 bg-[#77BF43] text-white rounded-lg text-xs font-bold">
                            {{ $currentPage }} / {{ $totalPages }}
                        </div>

                        {{-- Botón Siguiente --}}
                        @if ($currentPage < $totalPages)
                            <button 
                                wire:click="nextPage"
                                class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 text-xs font-medium text-gray-700 flex items-center justify-center gap-1 shadow-sm">
                                <span>Siguiente</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <span class="flex-1 px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed flex items-center justify-center gap-1">
                                <span>Siguiente</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
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