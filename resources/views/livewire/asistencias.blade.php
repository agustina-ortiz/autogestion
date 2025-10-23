<div>
    @push('styles')
    <style>
        .asistencias-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .filtros-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .empleado-card {
            background: linear-gradient(135deg, #77BF43, #BED630);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(119, 191, 67, 0.3);
        }

        .empleado-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }

        .section-title {
            color: #77BF43;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .no-data {
            padding: 3rem;
            text-align: center;
            color: #999;
            font-size: 1.1rem;
        }

        .btn-mostrar {
            background: linear-gradient(135deg, #77BF43, #5da832);
            color: white;
            padding: 0.6rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(119, 191, 67, 0.3);
        }

        .btn-mostrar:hover {
            background: linear-gradient(135deg, #5da832, #77BF43);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(119, 191, 67, 0.5);
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table thead {
            background: linear-gradient(135deg, #77BF43, #BED630);
            color: white;
        }

        .custom-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .custom-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .custom-table td {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            color: #374151;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }
    </style>
    @endpush

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