<div class="pb-12 md:pb-2">
    <x-slot:title>Perfil - Sistema Autogestión</x-slot:title>

    <div class="min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header de la página -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Actualizar mis Datos</h1>
                <p class="mt-2 text-sm text-gray-600">Actualiza tu información personal y de contacto</p>
            </div>

            <!-- Mensajes de éxito/error -->
            @if (session()->has('success'))
                <div
                    x-data="{ show: true }"
                    x-init="
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => show = false, 5000);
                    "
                    x-show="show"
                    x-transition
                    class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between"
                    >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div 
                    x-data="{ show: true }"
                    x-init="
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => show = false, 5000);
                    "
                    x-show="show"
                    x-transition
                    class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between"
                    >   
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Formulario -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    
                    <!-- Foto de Perfil -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto de Perfil
                        </label>
                        <div class="flex items-center gap-6">
                            <!-- Foto actual -->
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center border-2 border-[#77bf43] shadow-lg overflow-hidden">
                                    <img id="foto-preview" 
                                         src="{{ $fotoActualUrl }}" 
                                         alt="Foto de perfil" 
                                         class="w-full h-full object-cover">
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex-1 space-y-3">
                                <!-- Input de archivo (oculto) -->
                                <input 
                                    type="file" 
                                    id="foto-input"
                                    accept="image/*"
                                    class="hidden"
                                >

                                <div class="flex gap-3">
                                    <!-- Botón para subir foto -->
                                    <label 
                                        for="foto-input"
                                        class="inline-flex items-center px-4 py-2 bg-[#77bf43] text-white rounded-lg hover:opacity-90 transition-opacity cursor-pointer text-sm font-medium"
                                    >
                                        <svg class="w-5 h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="hidden sm:inline">
                                            Subir foto
                                        </span>
                                    </label>

                                    <!-- Botón para eliminar foto -->
                                    @if($fotoActualUrl !== asset('images/no-foto.png'))
                                        <button 
                                            type="button"
                                            wire:click="eliminarFoto"
                                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium"
                                        >
                                            <svg class="w-5 h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span class="hidden sm:inline">
                                                Eliminar foto
                                            </span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Indicador de carga -->
                                <div id="foto-loading" class="hidden text-sm text-gray-600 flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2 text-[#77bf43]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subiendo imagen...
                                </div>

                                <!-- Mensaje de error (oculto por defecto) -->
                                <div id="foto-error" class="hidden text-sm text-red-600"></div>

                                <p class="text-xs text-gray-500">
                                    JPG, PNG o GIF. Máximo 2MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Nombre (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre y Apellido
                        </label>
                        <input 
                            type="text" 
                            value="{{ $nombre }}" 
                            readonly 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none"
                        >
                        <p class="mt-1 text-xs text-gray-500">Este campo no puede ser modificado</p>
                    </div>

                    <!-- Domicilio -->
                    <div>
                        <label for="domicilio" class="block text-sm font-medium text-gray-700 mb-2">
                            Domicilio
                        </label>
                        <input 
                            type="text" 
                            id="domicilio"
                            wire:model="domicilio"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition-colors @error('domicilio') border-red-500 @enderror"
                            placeholder="Ingresa tu domicilio"
                        >
                        @error('domicilio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono
                        </label>
                        <input 
                            type="text" 
                            id="telefono"
                            wire:model="telefono"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition-colors @error('telefono') border-red-500 @enderror"
                            placeholder="Ingresa tu teléfono"
                        >
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="mail" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="mail"
                            wire:model="mail"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition-colors @error('mail') border-red-500 @enderror"
                            placeholder="ejemplo@correo.com"
                        >
                        @error('mail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <button 
                            type="button"
                            wire:click="cancel"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-[#77bf43] text-white rounded-lg hover:opacity-90 transition-opacity font-medium flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            Si necesitas modificar tu nombre o apellido, por favor contacta al departamento de Recursos Humanos.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botón Volver al Inicio --}}
            <div class="flex justify-center mt-8 mb-6">
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
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoInput = document.getElementById('foto-input');
            const fotoPreview = document.getElementById('foto-preview');
            const fotoLoading = document.getElementById('foto-loading');
            const fotoError = document.getElementById('foto-error');

            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (!file) return;

                // Validar tipo de archivo
                if (!file.type.match('image.*')) {
                    fotoError.textContent = 'El archivo debe ser una imagen.';
                    fotoError.classList.remove('hidden');
                    setTimeout(() => fotoError.classList.add('hidden'), 5000);
                    fotoInput.value = '';
                    return;
                }

                // Validar tamaño (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    fotoError.textContent = 'La imagen no puede superar los 2MB.';
                    fotoError.classList.remove('hidden');
                    setTimeout(() => fotoError.classList.add('hidden'), 5000);
                    fotoInput.value = '';
                    return;
                }

                // Ocultar error si había uno
                fotoError.classList.add('hidden');

                // Mostrar loading
                fotoLoading.classList.remove('hidden');

                // Crear FormData
                const formData = new FormData();
                formData.append('foto', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Subir archivo
                fetch('{{ route("perfil.foto.subir") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    fotoLoading.classList.add('hidden');
                    
                    if (data.success) {
                        // Actualizar preview
                        fotoPreview.src = data.url;
                        
                        // Llamar a Livewire para actualizar el componente
                        @this.call('actualizarFoto');
                        
                        // Mostrar mensaje de éxito
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        
                        // Recargar la página para mostrar el mensaje flash
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        fotoError.textContent = data.message || 'Error al subir la foto.';
                        fotoError.classList.remove('hidden');
                        setTimeout(() => fotoError.classList.add('hidden'), 5000);
                    }
                    
                    // Limpiar input
                    fotoInput.value = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    fotoLoading.classList.add('hidden');
                    fotoError.textContent = 'Error al subir la foto. Por favor, intenta nuevamente.';
                    fotoError.classList.remove('hidden');
                    setTimeout(() => fotoError.classList.add('hidden'), 5000);
                    fotoInput.value = '';
                });
            });
        });
    </script>
    @endpush
</div>