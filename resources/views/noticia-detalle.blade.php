{{-- resources/views/noticia-detalle.blade.php --}}
<x-layouts.autogestion>
    <x-slot:title>{{ $noticia->TITULO }} - Sistema Autogestión</x-slot:title>

    <div class="min-h-screen pb-20 lg:pb-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Card de la Noticia -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header Rosa -->
                <div class="bg-[#ed5b9a] px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                        <h1 class="text-xl lg:text-2xl font-bold text-white flex items-center gap-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            {{ $noticia->TITULO }}
                        </h1>
                        <span class="text-sm text-white bg-white bg-opacity-20 px-3 py-1 rounded-full w-fit">
                            {{ \Carbon\Carbon::parse($noticia->FECHA)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 py-6 lg:px-8 lg:py-8">
                    <div class="prose prose-sm sm:prose lg:prose-lg max-w-none">
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $noticia->DETALLE }}
                        </div>
                    </div>

                    <!-- Links y Archivos -->
                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-4">
                        @if($noticia->LINK)
                            <a href="{{ $noticia->LINK }}" 
                               target="_blank"
                               class="flex items-center gap-3 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors group">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#77BF43] bg-opacity-10 rounded-full flex items-center justify-center group-hover:bg-opacity-20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                                <span class="break-all">Ver más información</span>
                            </a>
                        @endif

                        @if($noticia->ARCHIVO)
                            <a href="{{ asset('storage/noticias/' . $noticia->ARCHIVO) }}"
                               target="_blank"
                               class="flex items-center gap-3 text-[#77BF43] hover:text-[#5a9532] font-semibold transition-colors group">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#77BF43] bg-opacity-10 rounded-full flex items-center justify-center group-hover:bg-opacity-20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="break-all">Ver archivo adjunto</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Botón Volver -->
        <div class="flex justify-center mt-2">
            <a href="{{ route('dashboard') }}" class="bg-gray-600 items-center justify-center rounded-md py-2 px-2 text-white inline-flex items-center gap-1 font-medium text-sm transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio
            </a>
        </div>
    </div>
</x-layouts.autogestion>