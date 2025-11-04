<!-- resources/views/livewire/asignaciones-familiares.blade.php -->
<div>
    <x-slot:title>Asignaciones Familiares - Sistema Autogestión</x-slot:title>

    <main class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#77BF43]">
                DDJJ para Asignaciones Familiares para Madres
            </h1>
            <p class="text-gray-600 mt-2">
                Período: {{ $periodo }}/{{ $anio }}
            </p>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

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
                                        wire:model="formularios.{{ $index }}.tipoadjunto"
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
                                    </label>
                                   <input 
                                        type="file" 
                                        wire:model="archivos.{{ $index }}"
                                        accept="image/*,application/pdf"
                                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#77BF43] file:text-white hover:file:bg-[#6AB03A] cursor-pointer"
                                    />
                                    @error("formularios.{$index}.nuevo_archivo")
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    
                                   @if(isset($archivos[$index]))
                                        <div class="mt-2 flex items-center text-sm text-green-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Archivo seleccionado</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Archivo actual -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="font-medium text-gray-800 mb-2">Archivo actual:</p>
                                @if($formularios[$index]['archivo_actual'] && $formularios[$index]['tipoadjunto'] != 4)
                                    @php
                                        $nombreArchivo = auth()->user()->LEGAJO . '_' . $anio . '_' . $periodo . '_' . $formularios[$index]['dnihijo'];
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
                                @else
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <p class="text-sm">No hay archivo cargado</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Botón guardar -->
                    <div class="mt-6 flex justify-end">
                        <button 
                            wire:click="guardarFormulario({{ $index }})"
                            wire:loading.attr="disabled"
                            wire:target="guardarFormulario({{ $index }})"
                            class="px-6 py-2 bg-[#77BF43] text-white font-semibold rounded-lg hover:bg-[#6AB03A] transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="guardarFormulario({{ $index }})">
                                Guardar Información
                            </span>
                            <span wire:loading wire:target="guardarFormulario({{ $index }})">
                                Guardando...
                            </span>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Botón volver -->
        <div class="mt-8">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio
            </a>
        </div>
    </main>
</div>