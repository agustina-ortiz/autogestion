<div class="min-h-screen">
    <div class="p-8 max-w-[1400px] mx-auto">

        {{-- Header con nombre de usuario --}}
        <div class="mb-6">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6 rounded-xl shadow-[0_2px_8px_rgba(119,191,67,0.3)]">
                <h3 class="text-xl font-semibold m-0">
                    Bienvenido/a, {{ Auth::user()->NOMBRE }}
                </h3>
            </div>
        </div>

        {{-- Mensaje de error --}}
        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filtros de fecha --}}
        <div class="mb-6">
            <div class="bg-white p-6 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] flex items-end gap-4 flex-wrap">
                <flux:field>
                    <flux:label>Fecha Desde</flux:label>
                    <flux:input type="date" wire:model.live="fechaDesde" />
                </flux:field>

                <flux:field>
                    <flux:label>Fecha Hasta</flux:label>
                    <flux:input type="date" wire:model.live="fechaHasta" />
                </flux:field>

                <button 
                    wire:click="limpiarFiltros" 
                    class="bg-gradient-to-r from-[#77BF43] to-[#5da832] text-white px-8 py-2.5 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0">
                    Limpiar Filtros
                </button>
            </div>
        </div>

        {{-- Tabla de compensatorios --}}
        <div class="mb-8">
            {{-- Header --}}
            <div class="flex flex-row justify-between">
                <h2 class="text-[#77BF43] text-2xl font-bold mb-4 uppercase">
                    Lista de Compensatorios
                </h2>
                <h3 class="text-[#77BF43] text-xl font-bold m-0">
                    Compensatorios Disponibles: {{ $dias }}
                </h3>
            </div>

            <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white">
                            <tr>
                                <th class="p-4 text-right font-semibold text-sm">#</th>
                                <th class="p-4 text-right font-semibold text-sm">Fecha</th>
                                <th class="p-4 text-right font-semibold text-sm">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($rows) > 0)
                                @php $i = $offset + 1; @endphp
                                @foreach ($rows as $row)
                                    <tr class="border-b border-[#e5e7eb] hover:bg-[#f9fafb]">
                                        <td class="py-3 px-4 text-right text-sm text-[#374151]">{{ $i }}</td>
                                        <td class="py-3 px-4 text-right text-sm text-[#374151]">
                                            {{ \Carbon\Carbon::parse($row->FECINASI)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-sm text-[#374151]">{{ $row->observaciones }}</td>
                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="p-12 text-center text-[#999] text-lg">
                                        No hay compensatorios disponibles
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    {{-- Paginación --}}
                    @if ($totalPages > 1)
                        <div class="bg-white py-4 px-4 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)]">
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
    </div>
</div>