<div class="p-4 sm:p-0">
    {{-- Header --}}
    <div class="mb-4 hidden md:block">
        <div class="bg-[#77BF43] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                    <span class="tracking-tight">Solicitudes</span>
                </h1>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Mensajes --}}
    @if (session()->has('success'))
        <div id="success-message" data-flash-success class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div id="error-message" data-flash-error class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Secciones Dinámicas de Solicitudes --}}
    @if(count($tiposMovimientoVigentes) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
            
            @foreach($tiposMovimientoVigentes as $tipo)
                @php
                    $color = $tipo->getColorSeccion();
                    $tienePendiente = $this->tieneSolicitudPendiente($tipo->id);
                @endphp
                
                <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col h-full">
                    <div class="bg-white text-white mt-4 p-1 sm:p-2 px-4 sm:px-6">
                        <h2 class="text-[#77BF43] text-lg sm:text-xl font-bold m-0 uppercase">{{ $tipo->tipo_movimiento }}</h2>
                    </div>
                    <hr class="w-5/6 mx-auto">
                    <div class="p-4 sm:p-6 flex flex-col flex-1">
                        <div class="bg-{{ $color['bg'] }} border-l-4 border-{{ $color['border'] }} p-3 sm:p-4 mb-4 sm:mb-6 rounded">
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                                <span class="font-bold text-{{ $color['text'] }}">{{ $tipo->requiereImporte() ? '¡ATENCIÓN!' : '¡IMPORTANTE!' }}</span> 
                                {!! $tipo->getDescripcionSeccion() !!}
                            </p>
                            @if($tipo->getTextoAcreditacion())
                                <p class="text-xs sm:text-sm text-gray-700 mt-2">
                                    {!! $tipo->getTextoAcreditacion() !!}
                                </p>
                            @endif
                        </div>
                        
                        <div class="mt-auto pt-0.5">
                            @if($tienePendiente)
                                <button 
                                    disabled
                                    title="Ya tienes una solicitud de {{ strtolower($tipo->tipo_movimiento) }} pendiente"
                                    class="w-full bg-gray-300 text-gray-500 px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-not-allowed border-0 block text-center opacity-60">
                                    Solicitar {{ $tipo->tipo_movimiento }}
                                </button>
                            @else
                                <button 
                                    wire:click="abrirModal({{ $tipo->id }})"
                                    class="w-full bg-[#77BF43] text-white px-6 sm:px-8 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:bg-[#5da832] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0 block text-center">
                                    Solicitar {{ $tipo->tipo_movimiento }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8 rounded">
            <p class="text-sm text-gray-700">
                No hay tipos de solicitudes disponibles en este momento.
            </p>
        </div>
    @endif

    {{-- Título de Mis Solicitudes --}}
    <h2 class="text-[#77BF43] text-xl sm:text-2xl font-bold mb-4 uppercase">
        Mis Solicitudes
    </h2>

    {{-- Vista de Tarjetas para Móvil --}}
    <div class="md:hidden space-y-4 mb-16">
        @if (count($solicitudes) > 0)
            @foreach ($solicitudes as $solicitud)
                <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden">
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $solicitud->color_badge }} text-{{ $solicitud->color_badge_text }}">
                                {{ $solicitud->tipo }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($solicitud->estado === 'Pendiente') bg-orange-100 text-orange-800
                                @elseif($solicitud->estado === 'Listo') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $solicitud->estado }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Fecha:</span>
                            <span>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y') }}</span>
                        </div>

                        @if($solicitud->monto)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium">Monto:</span>
                                <span class="font-semibold text-[#77BF43]">${{ number_format($solicitud->monto, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($solicitud->observaciones)
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Observaciones:</span>
                                <p class="mt-1 text-gray-500">{{ $solicitud->observaciones }}</p>
                            </div>
                        @endif

                        @if($solicitud->estado_raw === 1)
                            <div class="flex gap-2 pt-2 border-t border-gray-100">
                                @if($solicitud->requiere_importe)
                                    <button 
                                        wire:click="editarMonto({{ $solicitud->id }})"
                                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>
                                @endif
                                
                                <button 
                                    onclick="confirmarEliminacion({{ $solicitud->id }}, '{{ $solicitud->tipo }}')"
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 text-base">No tienes solicitudes registradas</p>
            </div>
        @endif
    </div>

    {{-- Tabla para Desktop --}}
    <div class="hidden md:block mb-16 md:mb-8">
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-[#77BF43] text-white">
                        <tr>
                            <th class="p-4 text-left font-semibold text-sm">#</th>
                            <th class="p-4 text-left font-semibold text-sm">Tipo</th>
                            <th class="p-4 text-left font-semibold text-sm">Fecha Solicitud</th>
                            <th class="p-4 text-left font-semibold text-sm">Estado</th>
                            <th class="p-4 text-left font-semibold text-sm">Monto</th>
                            <th class="p-4 text-left font-semibold text-sm">Observaciones</th>
                            <th class="p-4 text-left font-semibold text-sm">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($solicitudes) > 0)
                            @php $i = 1; @endphp
                            @foreach ($solicitudes as $solicitud)
                                <tr class="border-b border-[#e5e7eb] hover:bg-[#f9fafb]">
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">{{ $i }}</td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $solicitud->color_badge }} text-{{ $solicitud->color_badge_text }}">
                                            {{ $solicitud->tipo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($solicitud->estado === 'Pendiente') bg-orange-100 text-orange-800
                                            @elseif($solicitud->estado === 'Listo') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        @if($solicitud->monto)
                                            ${{ number_format($solicitud->monto, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        {{ $solicitud->observaciones ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm">
                                        <div class="flex items-center gap-2">
                                            @if($solicitud->requiere_importe && $solicitud->estado_raw === 1)
                                                <button 
                                                    wire:click="editarMonto({{ $solicitud->id }})" title="Editar"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200 inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            
                                            @if($solicitud->estado_raw === 1)
                                                <button 
                                                    onclick="confirmarEliminacion({{ $solicitud->id }}, '{{ $solicitud->tipo }}')" 
                                                    title="Eliminar"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200 inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            
                                            @if($solicitud->estado_raw !== 1)
                                                <span class="text-gray-400 text-xs italic">Sin acciones</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="p-12 text-center text-[#999] text-lg">
                                    No tienes solicitudes registradas
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Botón Volver --}}
    <div class="hidden md:flex justify-center mb-4">
        <a 
            href="{{ route('dashboard') }}" 
            class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.3)] hover:shadow-[0_4px_8px_rgba(0,0,0,0.5)] border-0 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Inicio
        </a>
    </div>

    {{-- Modal Dinámico --}}
    @if($mostrarModal && $tipoMovimientoSeleccionado)
        @php
            $colorModal = $tipoMovimientoSeleccionado->getColorSeccion();
        @endphp
        
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="cerrarModal">
            <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-md">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">Solicitar {{ $tipoMovimientoSeleccionado->tipo_movimiento }}</h3>
                
                <div class="space-y-4 mb-6">
                    @if($tipoMovimientoSeleccionado->requiereImporte())
                        {{-- Campo de monto --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                Importe (Máximo: ${{ number_format($tipoMovimientoSeleccionado->importe_maximo, 0, ',', '.') }})
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm sm:text-base">$</span>
                                <input 
                                    type="number" 
                                    wire:model="montoSolicitado"
                                    step="0.01"
                                    min="0"
                                    max="{{ $tipoMovimientoSeleccionado->importe_maximo }}"
                                    class="w-full pl-7 sm:pl-8 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                                    placeholder="Ingrese el monto">
                            </div>
                        </div>
                    @endif

                    @if($tipoMovimientoSeleccionado->permiteSeleccionarFormaPago())
                        {{-- Selector de forma de cobro --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                Forma de Cobro
                            </label>
                            <select 
                                wire:model="formaCobro"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent">
                                <option value="">Seleccione una opción</option>
                                <option value="deposito">Por Depósito</option>
                                <option value="cheque">Por Cheque</option>
                            </select>
                        </div>
                    @else
                        {{-- Mostrar forma de pago fija --}}
                        <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg">
                            <p class="text-xs sm:text-sm text-gray-700">
                                <span class="font-semibold">Forma de cobro:</span> 
                                {{ $tipoMovimientoSeleccionado->getFormaPagoTexto() }}
                            </p>
                        </div>
                    @endif

                    @if(!$tipoMovimientoSeleccionado->requiereImporte())
                        {{-- Información del usuario para tipos sin importe --}}
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                Apellido y Nombre
                            </label>
                            <input 
                                type="text" 
                                value="{{ Auth::user()->NOMBRE }}"
                                disabled
                                class="w-full px-3 sm:px-4 py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                Legajo
                            </label>
                            <input 
                                type="text" 
                                value="{{ Auth::user()->LEGAJO }}"
                                disabled
                                class="w-full px-3 sm:px-4 py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>
                    @endif

                    {{-- Mensaje informativo --}}
                    <div class="bg-{{ $colorModal['bg'] }} border-l-4 border-{{ $colorModal['border'] }} p-3 rounded">
                        <p class="text-xs text-gray-700">
                            {!! $tipoMovimientoSeleccionado->getTextoConfirmacionModal() !!}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <button 
                        wire:click="cerrarModal"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium text-sm sm:text-base">
                        Cancelar
                    </button>
                    <button 
                        wire:click="confirmarSolicitud"
                        class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5da832] transition-colors duration-200 font-medium text-sm sm:text-base">
                        Confirmar Solicitud
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Edición --}}
    @if($mostrarModalEdicion)
        @php
            $solicitudEditandoObj = collect($solicitudes)->firstWhere('id', $solicitudEditando);
            $tipoEdicion = $solicitudEditandoObj ? $tiposMovimientoVigentes->firstWhere('id', $solicitudEditandoObj->tipo_movimiento_id) : null;
        @endphp
        
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="cerrarModalEdicion">
            <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-md">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">Editar Monto</h3>
                
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                        Nuevo Monto @if($tipoEdicion)(Máximo: ${{ number_format($tipoEdicion->importe_maximo, 0, ',', '.') }})@endif
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm sm:text-base">$</span>
                        <input 
                            type="number" 
                            wire:model="montoEdicion"
                            step="0.01"
                            min="0"
                            @if($tipoEdicion) max="{{ $tipoEdicion->importe_maximo }}" @endif
                            class="w-full pl-7 sm:pl-8 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                            placeholder="Ingrese el nuevo monto">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <button 
                        wire:click="cerrarModalEdicion"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium text-sm sm:text-base">
                        Cancelar
                    </button>
                    <button 
                        wire:click="guardarEdicion"
                        class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5da832] transition-colors duration-200 font-medium text-sm sm:text-base">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <style>
        /* Estilos personalizados para Sweet Alert */
        .swal2-confirm.btn-eliminar {
            background-color: #ef4444 !important;
            color: white !important;
        }
        
        .swal2-confirm.btn-eliminar:hover {
            background-color: #dc2626 !important;
            transform: translateY(-2px);
        }
        
        .swal2-cancel.btn-cancelar {
            background-color: #6b7280 !important;
            color: white !important;
        }
        
        .swal2-cancel.btn-cancelar:hover {
            background-color: #4b5563 !important;
            transform: translateY(-2px);
        }

        /* Cambiar color del icono de warning/exclamación */
        .swal2-icon.swal2-warning {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }
        
        .swal2-icon.swal2-warning .swal2-icon-content {
            color: #ef4444 !important;
        }

        /* Márgenes laterales en móvil */
        .swal2-container {
            padding: 1rem !important;
        }
        
        @media (max-width: 640px) {
            .swal2-popup {
                width: calc(100% - 2rem) !important;
                margin: 0 1rem !important;
            }
        }
    </style>

    <script>
        function setupAutoDismiss(elementSelector, timeout = 3000) {
            const element = document.querySelector(elementSelector);
            if (element) {
                setTimeout(() => {
                    element.style.display = 'none';
                }, timeout);
            }
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            setupAutoDismiss('[data-flash-success]', 3000);
            setupAutoDismiss('[data-flash-error]', 3000);
        });

        document.addEventListener('livewire:initialized', () => {
            // Listener para cerrar Sweet Alert
            Livewire.on('cerrar-swal', () => {
                Swal.close();
            });

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    setTimeout(() => {
                        const errorMessage = document.querySelector('[data-flash-error]');
                        
                        if (errorMessage) {
                            errorMessage.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                            
                            setupAutoDismiss('[data-flash-error]', 3000);
                        }
                        
                        setupAutoDismiss('[data-flash-success]', 3000);

                    }, 100);
                });
            });
        });

        // Función para confirmar eliminación con Sweet Alert
        function confirmarEliminacion(solicitudId, tipoSolicitud) {
            Swal.fire({
                title: '¿Está seguro?',
                html: `¿Desea eliminar esta solicitud de <strong>${tipoSolicitud}</strong>?<br><small class="text-gray-500">Esta acción no se puede deshacer</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-xl shadow-2xl',
                    title: 'text-gray-800 font-bold text-xl',
                    htmlContainer: 'text-gray-600',
                    confirmButton: 'btn-eliminar ml-6 px-6 py-2.5 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300',
                    cancelButton: 'btn-cancelar px-6 py-2.5 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300'
                },
                buttonsStyling: false,
                reverseButtons: true,
                focusCancel: true,
                allowOutsideClick: true,
                allowEscapeKey: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llamar a Livewire para eliminar
                    @this.eliminarSolicitud(solicitudId);
                }
            });
        }
    </script>
    @endpush
</div>