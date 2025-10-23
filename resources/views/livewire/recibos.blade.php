<div class="min-h-screen bg-gradient-to-br from-[#91D5E2]/60 via-white to-[#BED630]/30 backdrop-blur-sm">
    <div class="container mx-auto px-6 py-10">

        {{-- Header con nombre de usuario --}}
        <div class="mb-8">
            <div class="relative bg-white/70 backdrop-blur-lg shadow-lg border border-white/50 rounded-2xl px-8 py-4">
                <div class="absolute -top-4 -left-4 bg-[#77BF43] text-white px-3 py-1 text-xs font-semibold rounded-md shadow-md">
                    Panel de Usuario
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Mis Recibos</h1>
                <p class="text-gray-600 mt-3 text-lg">
                    Bienvenido/a, 
                    <span class="font-semibold text-[#77BF43]">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>

        {{-- Mensaje de error si existe --}}
        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabla de recibos --}}
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden border border-gray-100/60">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-8 py-5 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-white opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M4 6h16M4 6v14a2 2 0 002 2h12a2 2 0 002-2V6" />
                    </svg>
                    Lista de Recibos
                </h2>
                <div class="text-sm text-white/80">
                    {{ $totalRecords }} registros
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-[#91D5E2]/30 text-gray-700 uppercase text-xs font-semibold tracking-wide">
                        <tr>
                            <th class="px-6 py-4 text-center">#</th>
                            <th class="px-6 py-4 text-center">Año</th>
                            <th class="px-6 py-4 text-center">Mes</th>
                            <th class="px-6 py-4 text-left">Tipo</th>
                            <th class="px-6 py-4 text-center">Recibo N°</th>
                            <th class="px-6 py-4 text-center">Importe Neto</th>
                            <th class="px-6 py-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/50">
                        @if (count($rows) > 0)
                            @php $i = $offset + 1; @endphp
                            @foreach ($rows as $row)
                                <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-150">
                                    <td class="px-6 py-4 text-center font-medium">{{ $i }}</td>
                                    <td class="px-6 py-4 text-center">{{ $row['ANIO'] }}</td>
                                    <td class="px-6 py-4 text-center">{{ $row['MES'] }}</td>
                                    <td class="px-6 py-4 text-left">{{ $row['TIPO_LIQ'] }}</td>
                                    <td class="px-6 py-4 text-center">{{ $row['NRO_RECIBO'] }}</td>
                                    <td class="px-6 py-4 text-center font-semibold">
                                        ${{ number_format($row['LIQUIDO'], 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                       <a href="{{ route('recibo', ['numero' => $row['NRO_RECIBO'], 'anio' => $row['ANIO'], 'mes' => $row['MES'], 'tipo' => $row['TIPO_LIQ']]) }}"
                                        class="inline-flex items-center gap-2 bg-[#77BF43] hover:bg-[#6BA939] text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 text-xs shadow-sm">
                                            <i class="fa fa-eye"></i> VER
                                        </a>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No hay recibos disponibles
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($totalPages > 1)
                <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-200/70">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Mostrando {{ $offset + 1 }} - {{ min($offset + $perPage, $totalRecords) }} de {{ $totalRecords }} resultados
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            {{-- Botón Primera Página --}}
                            @if ($currentPage > 1)
                                <button 
                                    wire:click="gotoPage(1)"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                    </svg>
                                    Primero
                                </button>
                            @else
                                <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                    </svg>
                                    Primero
                                </span>
                            @endif

                            {{-- Botón Anterior --}}
                            @if ($currentPage > 1)
                                <button 
                                    wire:click="previousPage"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Anterior
                                </button>
                            @else
                                <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Anterior
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
                                        class="px-4 py-2 border border-[#77BF43] rounded-lg transition-colors text-sm font-medium bg-[#77BF43] text-white">
                                        {{ $i }}
                                    </button>
                                @else
                                    <button 
                                        wire:click="gotoPage({{ $i }})"
                                        class="px-4 py-2 border border-gray-300 rounded-lg transition-colors text-sm font-medium bg-white text-gray-700 hover:bg-gray-50">
                                        {{ $i }}
                                    </button>
                                @endif
                            @endfor

                            {{-- Botón Siguiente --}}
                            @if ($currentPage < $totalPages)
                                <button 
                                    wire:click="nextPage"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700 flex items-center gap-1">
                                    Siguiente
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            @else
                                <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    Siguiente
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            @endif

                            {{-- Botón Última Página --}}
                            @if ($currentPage < $totalPages)
                                <button 
                                    wire:click="gotoPage({{ $totalPages }})"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700 flex items-center gap-1">
                                    Último
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            @else
                                <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    Último
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>