<div>
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
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
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
                                    @if($nuevaFoto)
                                        <img src="{{ $nuevaFoto->temporaryUrl() }}" 
                                             alt="Nueva foto" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ $fotoActualUrl }}" 
                                             alt="Foto actual" 
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex-1 space-y-3">
                                <!-- Input de archivo (oculto) -->
                                <input 
                                    type="file" 
                                    id="foto-input"
                                    wire:model="nuevaFoto"
                                    accept="image/*"
                                    class="hidden"
                                >

                                <div class="flex gap-3">
                                    <!-- Botón para subir foto -->
                                    <label 
                                        for="foto-input"
                                        class="inline-flex items-center px-4 py-2 bg-[#77bf43] text-white rounded-lg hover:opacity-90 transition-opacity cursor-pointer text-sm font-medium"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $nuevaFoto ? 'Cambiar foto' : 'Subir foto' }}
                                    </label>

                                    <!-- Botón para eliminar foto -->
                                    @if($fotoActualUrl !== asset('images/no-foto.png'))
                                        <button 
                                            type="button"
                                            wire:click="eliminarFoto"
                                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium"
                                        >
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar foto
                                        </button>
                                    @endif
                                </div>

                                <!-- Indicador de carga -->
                                <div wire:loading wire:target="nuevaFoto" class="text-sm text-gray-600 flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2 text-[#77bf43]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Cargando imagen...
                                </div>

                                <p class="text-xs text-gray-500">
                                    JPG, PNG o GIF. Máximo 2MB.
                                </p>

                                @error('nuevaFoto')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
        </div>
    </div>
</div>