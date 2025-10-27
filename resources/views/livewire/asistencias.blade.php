<div>

    {{-- Header con nombre de usuario --}}
    <div class="mb-4">
        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
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
    <div class="filtros-section">
        <flux:field>
            <flux:label>Fecha Desde</flux:label>
            <flux:input type="date" wire:model="fechaDesde" />
        </flux:field>

        <flux:field>
            <flux:label>Fecha Hasta</flux:label>
            <flux:input type="date" wire:model="fechaHasta" />
        </flux:field>

        <flux:button wire:click="mostrar" class="btn-mostrar">
            Mostrar
        </flux:button>
    </div>


    <!-- Tabla de Fichadas -->
    <h2 class="section-title">Fichadas</h2>
    <div class="table-container">
        @if(count($fichadas) > 0)
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tarjeta</th>
                        <th>Fecha y Hora</th>
                        <th>Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fichadas as $index => $fichada)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $fichada->codtar }}</td>
                            <td>
                                @if($fichada->tipo == 'F')
                                    {{ \Carbon\Carbon::parse($fichada->fecha)->format('d/m/Y') }} {{ $fichada->hora }}
                                @else
                                    {{ \Carbon\Carbon::parse($fichada->fecha)->format('d/m/Y') }} - {{ $fichada->hora }}
                                @endif
                            </td>
                            <td>
                                @if(trim($fichada->certifi) != '')
                                    <a href="{{ $fichada->certifi }}" target="_blank" class="text-blue-600 hover:underline">
                                        Ver certificado
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No hay fichadas para mostrar en el período seleccionado</div>
        @endif
    </div>

    <!-- Tabla de Novedades -->
    @php
        $year = \Carbon\Carbon::parse($fechaDesde)->year;
    @endphp

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
    <div class="flex justify-center">
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