<!-- resources/views/livewire/asignaciones-familiares.blade.php -->
<div class="pb-8">
    <x-slot:title>Asignaciones Familiares - Sistema Autogestión</x-slot:title>

    <main class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-black">
                DDJJ para Asignaciones Familiares para Madres
            </h1>
            <p class="text-gray-600 mt-2">
                Período: {{ $periodo }}/{{ $anio }}
            </p>
        </div>

        @if (session()->has('success'))
            <div x-data="{ show: true }"
                    x-init="
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => show = false, 5000);
                    "
                    x-show="show"
                    x-transition
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- AGREGAR ESTE BLOQUE NUEVO -->
        @if ($errors->any())
            <div x-data="{ show: true }"
                x-init="window.scrollTo({ top: 0, behavior: 'smooth' })"
                x-show="show"
                x-transition
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-medium mb-2">Por favor corrija los siguientes errores:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Mensaje dinámico desde JavaScript -->
        <div id="mensaje-dinamico" style="display: none;" class="mb-6 flex items-center"></div>

        @if(count($hijos) === 0)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                <p class="font-medium">No se encontraron hijos registrados en el sistema.</p>
            </div>
        @else
            @foreach($hijos as $index => $hijo)
                <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 border-[#77BF43]">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Información de los progenitores - Hijo/a {{ $index + 1 }}
                        </h2>
                        @if($formularios[$index]['ok'] == 1)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                Aprobado
                            </span>
                        @elseif($formularios[$index]['respuesta'])
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">
                                En revisión
                            </span>
                        @endif
                    </div>

                    @if($formularios[$index]['respuesta'])
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-blue-800">
                                <strong>Respuesta RRHH:</strong> {{ $formularios[$index]['respuesta'] }}
                            </p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna izquierda: Datos del hijo y progenitor -->
                        <div class="space-y-4">
                            <!-- Datos del hijo (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Hijo/a Nombre:
                                </label>
                                <input 
                                    type="text" 
                                    value="{{ $hijo['nombre'] }}" 
                                    readonly 
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    DNI:
                                </label>
                                <input 
                                    type="text" 
                                    value="{{ $hijo['dni'] }}" 
                                    readonly 
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed"
                                >
                            </div>

                            <!-- Checkbox mismo progenitor (solo si no es el primer hijo) -->
                            @if($index > 0)
                                <div class="flex items-center py-2">
                                    <input 
                                        type="checkbox" 
                                        id="mismo-progenitor-{{ $index }}"
                                        wire:model.live="mismoProgenitor.{{ $index }}"
                                        class="w-4 h-4 text-[#77BF43] border-gray-300 rounded focus:ring-[#77BF43]"
                                    >
                                    <label for="mismo-progenitor-{{ $index }}" class="ml-2 text-sm text-gray-700">
                                        Mismo Progenitor (padre) que anterior hijo/a
                                    </label>
                                </div>
                            @endif

                            <!-- Datos del progenitor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Progenitor (padre): <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="formularios.{{ $index }}.nombrepadre"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                                    placeholder="Nombre completo del progenitor o 'No posee'"
                                >
                                @error("formularios.{$index}.nombrepadre")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    DNI Progenitor: <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="formularios.{{ $index }}.dnipadre"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                                    placeholder="12345678 o 11111111 si no posee"
                                    maxlength="8"
                                >
                                @error("formularios.{$index}.dnipadre")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    CUIL Progenitor: <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="formularios.{{ $index }}.cuilpadre"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                                    placeholder="20123456789 o 'No posee'"
                                    maxlength="11"
                                >
                                @error("formularios.{$index}.cuilpadre")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna derecha: Adjuntos -->
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="font-medium text-gray-800 mb-2">Adjunto:</p>
                                <p class="text-sm text-gray-600 mb-3">
                                    Adjunte la foto de una de estas opciones:
                                </p>
                                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1 mb-4">
                                    <li>Foto de recibo de sueldo</li>
                                    <li>Foto de constancia de monotributo</li>
                                    <li>Certificación negativa de ANSES</li>
                                </ul>

                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Estoy adjuntando: <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        wire:model.live="formularios.{{ $index }}.tipoadjunto"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                                    >
                                        <option value="">Seleccione una opción</option>
                                        @foreach($tiposAdjunto as $valor => $etiqueta)
                                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    @error("formularios.{$index}.tipoadjunto")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Seleccionar archivo: 
                                        @if($formularios[$index]['tipoadjunto'] != 4)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input 
                                        type="file" 
                                        id="archivo-{{ $index }}"
                                        accept="image/*,application/pdf"
                                        @if($formularios[$index]['tipoadjunto'] == 4) disabled @endif
                                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#77BF43] file:text-white hover:file:bg-[#6AB03A] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        onchange="archivoSeleccionado({{ $index }})"
                                    />
                                    <!-- Indicador de archivo seleccionado -->
                                    <div id="archivo-seleccionado-{{ $index }}" style="display: none;" class="mt-2 flex items-center justify-between text-sm bg-green-50 border border-green-200 rounded p-2">
                                        <div class="flex items-center text-green-700">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span id="nombre-archivo-{{ $index }}"></span>
                                        </div>
                                        <button 
                                            type="button"
                                            onclick="limpiarArchivo({{ $index }})"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error("formularios.{$index}.archivo_requerido")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    
                                    @if($formularios[$index]['tipoadjunto'] == 4)
                                        <p class="text-xs text-gray-500 mt-1">
                                            No es necesario cargar archivo para esta opción
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Archivo actual -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium text-gray-800 mb-2">Archivo actual:</p>
                                @if($formularios[$index]['archivo_actual'] && $formularios[$index]['tipoadjunto'] != 4)
                                    @php
                                        $nombreArchivo = auth()->user()->LEGAJO . '' . $anio . '' . $periodo . '_' . $formularios[$index]['dnihijo'];
                                        $extensiones = ['jpg', 'jpeg', 'png', 'pdf'];
                                        $archivoEncontrado = null;
                                        
                                        foreach($extensiones as $ext) {
                                            $path = "asignaciones-familiares/{$nombreArchivo}.{$ext}";
                                            if(Storage::disk('public')->exists($path)) {
                                                $archivoEncontrado = $path;
                                                break;
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-gray-700">
                                                    {{ $tiposAdjunto[$formularios[$index]['archivo_actual']] ?? 'Archivo cargado' }}
                                                </p>
                                                @if($archivoEncontrado)
                                                    <a href="{{ Storage::url($archivoEncontrado) }}" 
                                                       target="_blank" 
                                                       class="text-xs text-blue-600 hover:underline">
                                                        Ver archivo
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <button 
                                            type="button"
                                            onclick="eliminarArchivo({{ $index }})"
                                            class="text-red-600 hover:text-red-800 p-1"
                                            title="Eliminar archivo"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <p class="text-sm">
                                            @if($formularios[$index]['tipoadjunto'] == 4)
                                                No es necesario cargar archivo
                                            @else
                                                No hay archivo cargado
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Loading general -->
            <div id="loading-general" style="display: none;" class="mb-4 flex justify-center items-center text-blue-600">
                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Subiendo archivos...</span>
            </div>

            <!-- Botón guardar TODOS los formularios -->
            <div class="flex justify-center">
                <button 
                    onclick="event.preventDefault(); validarYGuardar()"
                    class="px-8 py-3 bg-[#77BF43] text-white font-semibold rounded-lg hover:bg-[#6AB03A] transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Guardar toda la información
                </button>
            </div>

        @endif

        <!-- Botón volver -->
        <div class="mt-2 flex justify-center">
            <a href="{{ route('hijos') }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Hijos
            </a>
        </div>
    </main>

    @push('scripts')
    <script>
        // Obtener el token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Cargar formularios en JavaScript
        const formularios = @json($formularios);
        
        // Almacenar archivos seleccionados temporalmente
        const archivosPendientes = {};
        
        /**
         * Guardar referencia del archivo seleccionado (NO lo sube aún)
         */
        function archivoSeleccionado(index) {
            const fileInput = document.getElementById(`archivo-${index}`);
            const file = fileInput.files[0];
            
            if (file) {
                // Guardar el archivo en memoria para subirlo después
                archivosPendientes[index] = file;
                
                // Mostrar indicador visual de que hay un archivo seleccionado
                document.getElementById(`archivo-seleccionado-${index}`).style.display = 'flex';
                document.getElementById(`nombre-archivo-${index}`).textContent = file.name;
            }
        }
        
        /**
         * Limpiar archivo seleccionado
         */
        function limpiarArchivo(index) {
            const fileInput = document.getElementById(`archivo-${index}`);
            fileInput.value = '';
            delete archivosPendientes[index];
            document.getElementById(`archivo-seleccionado-${index}`).style.display = 'none';
        }

        /**
        * Validar campos básicos ANTES de subir archivos
        */
        async function validarYGuardar() {
            // SIEMPRE validar primero los campos básicos
            const esValido = await @this.call('validarCamposBasicos');
            
            if (!esValido) {
                // Verificar si hay archivos pendientes seleccionados
                const indicesArchivos = Object.keys(archivosPendientes);
                
                if (indicesArchivos.length > 0) {
                    // Mensaje específico cuando hay archivos seleccionados pero faltan datos
                    mostrarMensaje(
                        'Ha seleccionado archivos, pero hay campos obligatorios sin completar. Por favor complete todos los datos del progenitor (nombre, DNI, CUIL y tipo de adjunto) antes de continuar.', 
                        'error'
                    );
                }
                
                // Scroll al inicio para ver errores de validación
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            
            // Si la validación básica pasó, entonces subir archivos
            const exitoso = await subirTodosLosArchivos();
            
            if (exitoso) {
                // Finalmente guardar todo
                @this.call('guardarTodosLosFormularios');
            }
        }
        
        /**
        * Subir todos los archivos pendientes vía AJAX
        */
        async function subirTodosLosArchivos() {
            const indices = Object.keys(archivosPendientes);
            
            if (indices.length === 0) {
                return true; // No hay archivos para subir
            }
            
            // Mostrar loading general
            document.getElementById('loading-general').style.display = 'flex';
            
            let todosExitosos = true;
            
            for (const index of indices) {
                const file = archivosPendientes[index];
                
                const formData = new FormData();
                formData.append('archivo', file);
                formData.append('index', index);
                formData.append('dnihijo', formularios[index]['dnihijo']);
                formData.append('anio', '{{ $anio }}');
                formData.append('periodo', '{{ $periodo }}');
                
                try {
                    const response = await fetch('{{ route("asignaciones-familiares.subir-archivo") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        mostrarMensaje(`Error al subir archivo del Hijo/a ${parseInt(index) + 1}: ${data.message}`, 'error');
                        todosExitosos = false;
                        break;
                    }
                    
                    // Actualizar estado en Livewire Y ESPERAR la sincronización
                    await @this.set('formularios.' + index + '.archivo_actual', formularios[index]['tipoadjunto']);
                    
                } catch (error) {
                    console.error('Error:', error);
                    mostrarMensaje(`Error al subir archivo del Hijo/a ${parseInt(index) + 1}`, 'error');
                    todosExitosos = false;
                    break;
                }
            }
            
            // Ocultar loading general
            document.getElementById('loading-general').style.display = 'none';
            
            if (todosExitosos) {
                // Limpiar archivos pendientes
                Object.keys(archivosPendientes).forEach(index => {
                    delete archivosPendientes[index];
                    const fileInput = document.getElementById(`archivo-${index}`);
                    if (fileInput) fileInput.value = '';
                    const indicador = document.getElementById(`archivo-seleccionado-${index}`);
                    if (indicador) indicador.style.display = 'none';
                });
            }
            
            return todosExitosos;
        }
        
        /**
         * Eliminar archivo vía AJAX
         */
        function eliminarArchivo(index) {
            if (!confirm('¿Está seguro que desea eliminar este archivo?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('index', index);
            formData.append('dnihijo', formularios[index]['dnihijo']);
            formData.append('anio', '{{ $anio }}');
            formData.append('periodo', '{{ $periodo }}');
            
            fetch('{{ route("asignaciones-familiares.eliminar-archivo") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    @this.call('archivoEliminadoJS', index);
                } else {
                    mostrarMensaje(data.message || 'Error al eliminar el archivo', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al eliminar el archivo', 'error');
            });
        }
        
        /**
         * Mostrar mensaje temporal
         */
        function mostrarMensaje(mensaje, tipo) {
            const contenedor = document.getElementById('mensaje-dinamico');
            
            let clases = '';
            if (tipo === 'success') {
                clases = 'bg-green-100 border border-green-400 text-green-700';
            } else if (tipo === 'error') {
                clases = 'bg-red-100 border border-red-400 text-red-700';
            }
            
            contenedor.className = `${clases} px-4 py-3 rounded-lg mb-6 flex items-center`;
            contenedor.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                ${mensaje}
            `;
            
            contenedor.style.display = 'flex';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            setTimeout(() => {
                contenedor.style.display = 'none';
            }, 5000);
        }
        
        // Interceptar el click del botón guardar
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('antes-de-guardar', async () => {
                const exitoso = await subirTodosLosArchivos();
                if (exitoso) {
                    @this.call('guardarTodosLosFormularios');
                }
            });
        });
    </script>
    @endpush
</div>