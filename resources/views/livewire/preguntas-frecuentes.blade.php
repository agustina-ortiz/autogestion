<!-- resources/views/livewire/preguntas-frecuentes.blade.php -->
<div>
    <x-slot:title>Preguntas Frecuentes - Sistema Autogestión</x-slot:title>

    <main class="max-w-5xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-3xl font-bold text-[#77BF43] mb-3">
                Preguntas Frecuentes
            </h1>
            <p class="text-gray-600 leading-relaxed">
                Encontrá respuestas rápidas a las consultas más comunes sobre el sistema de Autogestión. 
                Si no encontrás lo que buscás, podés contactarnos a través de la sección CONTACTO.
            </p>
        </div>

        <!-- Buscador -->
        <div class="mb-1">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    placeholder="Buscar en preguntas frecuentes..."
                    class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent"
                >
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                
                @if($busqueda)
                    <button 
                        wire:click="limpiarBusqueda"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
            
            @if($busqueda)
                <p class="text-sm text-gray-600 mt-2">
                    Mostrando resultados para: <span class="font-semibold">"{{ $busqueda }}"</span>
                </p>
            @endif
        </div>

        <!-- Lista de Preguntas -->
        <div class="space-y-3">
            @forelse($preguntasFiltradas as $pregunta)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-md">
                    <button 
                        wire:click="togglePregunta({{ $pregunta->id }})"
                        class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start gap-4 flex-1">
                            <span class="flex-shrink-0 w-8 h-8 bg-[#77BF43] text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ $pregunta->id }}
                            </span>
                            <h3 class="font-semibold text-gray-800 text-lg">
                                {{ $pregunta->pregunta }}
                            </h3>
                        </div>
                        <svg 
                            class="w-6 h-6 text-[#77BF43] flex-shrink-0 transition-transform duration-200 {{ $preguntaExpandida === $pregunta->id ? 'transform rotate-180' : '' }}"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    @if($preguntaExpandida === $pregunta->id)
                        <div class="px-6 pb-5 pt-2 bg-gray-50 border-t border-gray-100">
                            <div class="ml-12">
                                <p class="text-gray-700 leading-relaxed">
                                    {{ $pregunta->respuesta }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium text-gray-800 mb-1">No se encontraron resultados</p>
                    <p class="text-sm text-gray-600">
                        Intentá con otros términos de búsqueda o 
                        <button wire:click="limpiarBusqueda" class="text-[#77BF43] hover:underline font-medium">
                            ver todas las preguntas
                        </button>
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Ayuda adicional -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-800 mb-1">¿No encontraste lo que buscabas?</h3>
                    <p class="text-sm text-gray-700">
                        Si tenés alguna consulta que no está respondida aquí, podés comunicarte con Recursos Humanos 
                        a través de la sección CONTACTO.
                    </p>
                </div>
            </div>
        </div>

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