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
                                            <a 
                                                href="{{ route('planilla.descargar', ['dni' => $hijo->dni, 'nombre' => $hijo->nombre]) }}"
                                                target="_blank"
                                                class="bg-[#00b3ea] hover:bg-[#07a5d5] text-white font-bold py-2 px-4 rounded text-sm inline-block">
                                                Ver
                                            </a>

                                            <button 
                                                wire:click="seleccionarHijo({{ $hijo->dni }}, '{{ $hijo->nombre }}')"
                                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm">
                                                Subir
                                            </button>

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

        {{-- Modal para subir planilla - AJAX PURO --}}
        @if($selectedDni)
        <div id="modal-upload" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" onclick="cerrarModalUpload(event)">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[80vh] overflow-y-auto p-6 relative" onclick="event.stopPropagation()">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    Subir Planilla de Escolaridad
                </h2>

                <form id="form-planilla" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="dni" value="{{ $selectedDni }}">
                    <input type="hidden" name="planilla" value="{{ $planillaActual }}">
                    <input type="hidden" name="anio" value="{{ $anioActual }}">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Nombre del Hijo</label>
                            <input 
                                type="text" 
                                value="{{ $selectedNombre }}" 
                                disabled
                                class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-700 cursor-not-allowed"
                            >
                        </div>

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

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Archivo de la Planilla (JPG, PNG o PDF)
                            </label>
                            <input 
                                type="file" 
                                name="foto"
                                id="foto-input"
                                accept="image/jpeg,image/jpg,image/png,application/pdf"
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                required
                                onchange="mostrarVistaPrevia(this)"
                            >
                            <div id="error-foto" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>

                        <div id="vista-previa" class="hidden">
                            <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                            <div id="preview-container"></div>
                        </div>

                        <div id="loading-indicator" class="hidden">
                            <div class="flex items-center justify-center space-x-2 text-blue-600">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Subiendo archivo...</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                        <button 
                            type="button" 
                            onclick="cerrarModalUpload()"
                            id="btn-cancelar"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button 
                            type="button"
                            onclick="subirPlanillaAjax()"
                            id="btn-guardar"
                            class="bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Modal para VER planilla subida --}}
        @if($modalVerPlanilla && $rutaPlanillaVer)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" wire:click="cerrarModalVer">
            <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
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
                    @if($extensionPlanillaVer === 'pdf')
                        <iframe 
                            src="{{ $rutaPlanillaVer }}" 
                            class="w-full h-[600px] border-2 border-gray-300 rounded"
                        ></iframe>
                    @else
                        <img 
                            src="{{ $rutaPlanillaVer }}" 
                            alt="Planilla de Escolaridad"
                            class="max-w-full h-auto border-2 border-gray-300 rounded shadow-lg"
                        >
                    @endif
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mensajeExito = document.getElementById('flash-mensaje');
            if (mensajeExito) {
                setTimeout(function() {
                    mensajeExito.style.opacity = '0';
                    setTimeout(() => mensajeExito.remove(), 500);
                }, 2500);
            }

            const mensajeError = document.getElementById('flash-error');
            if (mensajeError) {
                setTimeout(function() {
                    mensajeError.style.opacity = '0';
                    setTimeout(() => mensajeError.remove(), 500);
                }, 2500);
            }
        });

        function cerrarModalUpload(event) {
            if (event && event.target.id !== 'modal-upload') return;
            @this.call('cerrarModal');
        }

        function mostrarVistaPrevia(input) {
            const vistaPrevia = document.getElementById('vista-previa');
            const previewContainer = document.getElementById('preview-container');
            const errorFoto = document.getElementById('error-foto');
            
            errorFoto.classList.add('hidden');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSize = file.size / 1024 / 1024;
                const fileName = file.name.toLowerCase();
                
                if (fileSize > 10) {
                    errorFoto.textContent = 'El archivo no debe superar 10MB';
                    errorFoto.classList.remove('hidden');
                    input.value = '';
                    vistaPrevia.classList.add('hidden');
                    return;
                }
                
                const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                const extension = fileName.split('.').pop();
                if (!validExtensions.includes(extension)) {
                    errorFoto.textContent = 'Solo se permiten archivos JPG, PNG o PDF';
                    errorFoto.classList.remove('hidden');
                    input.value = '';
                    vistaPrevia.classList.add('hidden');
                    return;
                }
                
                vistaPrevia.classList.remove('hidden');
                
                if (extension === 'pdf') {
                    previewContainer.innerHTML = `
                        <div class="bg-gray-100 border rounded p-4 text-center">
                            <svg class="w-16 h-16 mx-auto text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Archivo PDF seleccionado</p>
                            <p class="text-xs text-gray-500 mt-1">${file.name}</p>
                        </div>
                    `;
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <img src="${e.target.result}" class="max-w-full border rounded shadow">
                            <p class="text-xs text-gray-500 mt-2">La imagen se convertirá automáticamente a formato JPG al subirla.</p>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function subirPlanillaAjax() {
            const form = document.getElementById('form-planilla');
            const fotoInput = document.getElementById('foto-input');
            const loadingIndicator = document.getElementById('loading-indicator');
            const btnGuardar = document.getElementById('btn-guardar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const errorFoto = document.getElementById('error-foto');
            
            errorFoto.classList.add('hidden');
            
            if (!fotoInput.files || !fotoInput.files[0]) {
                errorFoto.textContent = 'Debe seleccionar un archivo';
                errorFoto.classList.remove('hidden');
                return;
            }
            
            loadingIndicator.classList.remove('hidden');
            btnGuardar.disabled = true;
            btnCancelar.disabled = true;
            btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');
            
            const formData = new FormData(form);
            
            fetch('{{ route("planilla.subir") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                loadingIndicator.classList.add('hidden');
                
                if (data.success) {
                    window.location.reload();
                } else {
                    btnGuardar.disabled = false;
                    btnCancelar.disabled = false;
                    btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
                    
                    if (data.errors) {
                        let errorMsg = Object.values(data.errors).flat().join(', ');
                        errorFoto.textContent = errorMsg;
                    } else {
                        errorFoto.textContent = data.message || 'Error al subir el archivo';
                    }
                    errorFoto.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingIndicator.classList.add('hidden');
                btnGuardar.disabled = false;
                btnCancelar.disabled = false;
                btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
                
                errorFoto.textContent = 'Error de conexión: ' + error.toString();
                errorFoto.classList.remove('hidden');
            });
        }
    </script>
    @endpush
</div>