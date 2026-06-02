<div class="min-h-screen pb-24 lg:pb-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

        <!-- Encabezado -->
        <div class="bg-[#ed5b9a] rounded-t-xl px-6 py-4">
            <h1 class="text-xl lg:text-2xl font-bold text-white flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Mis Evaluaciones de Desempeño
            </h1>
        </div>

        <!-- Tabla de evaluaciones -->
        <div class="bg-white rounded-b-xl shadow-lg overflow-hidden">
            @if($evaluaciones->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No tenés evaluaciones registradas aún.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Puntuación</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($evaluaciones as $evaluacion)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($evaluacion->FECHA)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            @if($evaluacion->PUNTUACION >= 8) bg-green-100 text-green-700
                                            @elseif($evaluacion->PUNTUACION >= 5) bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $evaluacion->PUNTUACION }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $evaluacion->OBSERVA ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Paginación -->
            @if($evaluaciones->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $evaluaciones->links() }}
                </div>
            @endif

            <!-- Texto informativo -->
            <div class="px-6 py-6 border-t border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    ¿Cómo proceder?
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{-- Texto a completar por RRHH --}}
                    El texto informativo sobre cómo proceder con la evaluación será completado por Recursos Humanos.
                </p>
            </div>
        </div>

        <!-- Botón volver -->
        <div class="flex justify-center mt-4">
            <a href="{{ route('dashboard') }}" class="bg-gray-600 rounded-md py-2 px-3 text-white inline-flex items-center gap-1 font-medium text-sm transition-colors hover:bg-gray-700">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio
            </a>
        </div>

    </div>
</div>
