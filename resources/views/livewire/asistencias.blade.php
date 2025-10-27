<div>
    <div class="asistencias-container">
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

        <!-- Card del empleado -->
        <div class="empleado-card">
            <h3>{{ $empleado->name ?? 'Empleado' }}</h3>
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
    </div>
</div>