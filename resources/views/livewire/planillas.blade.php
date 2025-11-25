<div class="container mx-auto p-6">
    {{-- Mensajes de éxito/error --}}
    @if (session()->has('mensaje'))
        <div id="flash-mensaje" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 transition-opacity duration-500" role="alert">
            <span class="block sm:inline">{{ session('mensaje') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div id="flash-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 transition-opacity duration-500" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar mensaje de éxito
            const mensajeExito = document.getElementById('flash-mensaje');
            if (mensajeExito) {
                setTimeout(function() {
                    mensajeExito.style.opacity = '0';
                    setTimeout(() => mensajeExito.remove(), 500);
                }, 2500);
            }

            // Ocultar mensaje de error
            const mensajeError = document.getElementById('flash-error');
            if (mensajeError) {
                setTimeout(function() {
                    mensajeError.style.opacity = '0';
                    setTimeout(() => mensajeError.remove(), 500);
                }, 2500);
            }
        });

        // Para mensajes que aparecen después de acciones de Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    setTimeout(() => {
                        const mensajeExito = document.getElementById('flash-mensaje');
                        if (mensajeExito) {
                            mensajeExito.style.opacity = '0';
                            setTimeout(() => mensajeExito.remove(), 500);
                        }

                        const mensajeError = document.getElementById('flash-error');
                        if (mensajeError) {
                            mensajeError.style.opacity = '0';
                            setTimeout(() => mensajeError.remove(), 500);
                        }
                    }, 8500);
                });
            });
        });
    </script>
    @endpush

    {{-- Header con nombre de usuario --}}
    <div class="mb-6">
        <div class="bg-[#77BF43] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Planilla de Escolaridad {{ $planillaActual }} - Año {{ $anioActual }}</span>
                    </h1>
                </div>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Verificar si es temporada de planillas --}}
    @if($planillaActual == 0)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p class="font-bold">Fuera de temporada</p>
            <p>Las planillas de escolaridad solo pueden gestionarse en los siguientes períodos:</p>
            <ul class="list-disc ml-6 mt-2">
                <li>Planilla 1: Febrero, Marzo, Abril</li>
                <li>Planilla 2: Noviembre, Diciembre, Enero</li>
            </ul>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6">
            @if(count($hijos) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead class="bg-[#77BF43] text-white">
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border">DNI</th>
                                <th class="px-4 py-2 border">Fecha Nacimiento</th>
                                <th class="px-4 py-2 border">Estado</th>
                                <th class="px-4 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hijos as $hijo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $hijo->nombre }}</td>
                                    <td class="px-4 py-2 border text-center">{{ $hijo->dni }}</td>
                                    <td class="px-4 py-2 border text-center">
                                        {{ $hijo->fecha_nac ? \Carbon\Carbon::parse($hijo->fecha_nac)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 border text-center">
                                        @if($hijo->estado_planilla === 'subida')
                                            <span class="bg-green-500 text-white px-2 py-1 rounded text-sm font-semibold">
                                                Subida
                                            </span>
                                        @elseif($hijo->estado_planilla === 'proceso')
                                            <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm font-semibold">
                                                En Proceso
                                            </span>
                                        @else
                                            <span class="bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border text-center">
                                        <div class="flex justify-center gap-2">
                                            {{-- Botón Descargar Planilla Vacía --}}
                                            <button 
                                                wire:click="descargarPlanilla({{ $hijo->dni }}, '{{ $hijo->nombre }}')"
                                                class="bg-[#00b3ea] hover:bg-[#07a5d5] text-white font-bold py-2 px-4 rounded text-sm">
                                                Descargar
                                            </button>

                                            {{-- Botón Subir Planilla --}}
                                            <button 
                                                wire:click="seleccionarHijo({{ $hijo->dni }}, '{{ $hijo->nombre }}')"
                                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm">
                                                Subir
                                            </button>

                                            {{-- Botón Ver Planilla (si ya está subida) --}}
                                            @if($hijo->tiene_planilla)
                                                <button 
                                                    wire:click="verPlanilla({{ $hijo->dni }})"
                                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                    👁️ Ver
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-gray-100 p-4 rounded">
                    <p class="text-gray-600">No se encontraron hijos registrados.</p>
                </div>
            @endif
            <!-- Botón volver -->
            <div class="mt-8 flex justify-center">
                <a href="{{ route('hijos') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Hijos
                </a>
            </div>
        </div>

        {{-- Modal para subir planilla --}}
        @if($selectedDni)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[80vh] overflow-y-auto p-6 relative" wire:click.stop>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    Subir Planilla de Escolaridad
                </h2>

                <form wire:submit.prevent="subirPlanilla" class="space-y-4">
                    {{-- Nombre del hijo --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Nombre del Hijo</label>
                        <input 
                            type="text" 
                            value="{{ $selectedNombre }}" 
                            disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-700 cursor-not-allowed"
                        >
                    </div>

                    {{-- DNI del hijo --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">DNI del Hijo</label>
                        <input 
                            type="text" 
                            value="{{ $selectedDni }}" 
                            disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-700 cursor-not-allowed"
                        >
                    </div>

                    {{-- Planilla actual --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Planilla N°</label>
                        <input 
                            type="text" 
                            value="{{ $planillaActual }}" 
                            disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-700 cursor-not-allowed"
                        >
                    </div>

                    {{-- Año --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Año</label>
                        <input 
                            type="text" 
                            value="{{ $anioActual }}" 
                            disabled
                            class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-700 cursor-not-allowed"
                        >
                    </div>

                    {{-- Archivo --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Imagen de la Planilla (JPG o PNG)</label>
                        <input 
                            type="file" 
                            wire:model="foto" 
                            accept="image/jpeg,image/jpg,image/png"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                        >
                        @error('foto') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Vista previa de la imagen --}}
                    @if($foto)
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                            <img src="{{ $foto->temporaryUrl() }}" class="max-w-full border rounded shadow">
                            <p class="text-xs text-gray-500 mt-2">
                                La imagen se convertirá automáticamente a formato JPG al subirla.
                            </p>
                        </div>
                    @endif

                    {{-- Botones --}}
                    <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                        <button 
                            type="button" 
                            wire:click="$set('selectedDni', null)"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Guardar</span>
                            <span wire:loading>Subiendo...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Modal para VER planilla subida --}}
        @if($modalVerPlanilla && $rutaPlanillaVer)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" wire:click="cerrarModalVer">
            <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto p-6" wire:click.stop>
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Planilla de Escolaridad Subida
                    </h2>
                    <button 
                        wire:click="cerrarModalVer"
                        class="text-gray-600 hover:text-gray-900 text-2xl font-bold">
                        ×
                    </button>
                </div>

                <div class="flex justify-center items-center bg-gray-100 p-4 rounded">
                    <img 
                        src="{{ $rutaPlanillaVer }}" 
                        alt="Planilla de Escolaridad"
                        class="max-w-full h-auto border-2 border-gray-300 rounded shadow-lg"
                    >
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <a 
                        href="{{ $rutaPlanillaVer }}" 
                        target="_blank"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        🔍 Ver en tamaño completo
                    </a>
                    <a 
                        href="{{ $rutaPlanillaVer }}" 
                        download
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        💾 Descargar
                    </a>
                    <button 
                        wire:click="cerrarModalVer"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- Modal de impresión --}}
    @if($mostrarModalImpresion && $contenidoImpresion)
    <div 
        class="modal-impresion fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50"
        x-data 
        x-init="$nextTick(() => { window.print(); $wire.set('mostrarModalImpresion', false) })"
    >
        <div class="bg-white p-8 rounded-lg shadow-lg w-[21cm] h-[29.7cm] overflow-auto print:w-full print:h-full print:shadow-none print:rounded-none">
            <style>
                @media print {
                    @page { 
                        size: A4; 
                        margin: 1cm; 
                    }
                }
            </style>

            <div class="text-center">
                <img src="{{ asset('img/encabezado.png') }}" class="mx-auto mb-4" style="max-width: 100%;">
                <h3 class="text-xl font-bold">Planilla de Escolaridad</h3>
                <h4 class="text-lg mb-6">Año Lectivo {{ $contenidoImpresion['anio'] }}</h4>
            </div>

            <p><strong>Apellido y Nombre del Padre / Madre:</strong> {{ $contenidoImpresion['nombrePadre'] }}</p>
            <p><strong>N° de Legajo:</strong> {{ $contenidoImpresion['legajo'] }}</p>
            <p><strong>D.N.I.:</strong> {{ Auth::user()->DNI ?? '-' }}</p>
            <br>
            <h4 class="text-center font-semibold">ASIGNACIONES FAMILIARES<br>CERTIFICADO LEY 24714</h4>
            <br>
            <p>
                CERTIFICO QUE: <strong>{{ $contenidoImpresion['nombre'] }}</strong><br>
                Ha sido registrado/a en este Establecimiento para cursar como alumno regular,
                durante el ciclo lectivo {{ $contenidoImpresion['anio'] }}.
            </p>
            <br>
            <p><strong>Nombre del Establecimiento:</strong> ____________________________________________</p>
            <p>☐ Establecimiento del ESTADO</p>
            <p>☐ Establecimiento incorporado o adscripto por Resolución N° ___________</p>
            <p><strong>Domicilio:</strong> ____________________________________________</p>
            <p><strong>Localidad:</strong> ____________________________________________</p>
            <br>
            <div class="text-right">
                <p>....................................................</p>
                <p><strong>Firma y Sello del Establecimiento</strong></p>
            </div>
            <br>
            <p>
                Este certificado debe presentarse en la oficina de Recursos Humanos antes del día 
                <strong>{{ $contenidoImpresion['planilla'] == 1 ? '30 de marzo' : '30 de diciembre' }}</strong>,
                caso contrario, de acuerdo a la Ley 24714, deberá cancelarse el pago del adicional por escolaridad.
            </p>
        </div>
    </div>
    @endif
</div>