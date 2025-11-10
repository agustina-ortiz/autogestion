<div>
    <x-slot:title>Hijos - Sistema Autogestión</x-slot:title>

    <main class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-black">
                Gestión de Hijos
            </h1>
            <p class="text-gray-600 mt-2">
                Accede a las planillas y declaraciones juradas relacionadas con tus hijos
            </p>
        </div>

        <!-- Botones principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Planillas -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-[#77BF43]">
                <div class="flex items-center gap-4 mb-4">
                    <svg class="w-12 h-12 stroke-[#77BF43]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Planillas</h2>
                        <p class="text-sm text-gray-600">Gestiona las planillas de tus hijos</p>
                    </div>
                </div>
                <a href="{{ route('planillas') }}" 
                   class="inline-flex items-center px-6 py-2 bg-[#77BF43] text-white font-semibold rounded-lg hover:bg-[#8cc4d8] transition-colors shadow-md">
                    Ir a Planillas
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <!-- DDJJ Asignaciones Familiares -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-[#77BF43]">
                <div class="flex items-center gap-4 mb-4">
                    <svg class="w-12 h-12 stroke-[#77BF43]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <circle cx="10" cy="13" r="1"/>
                        <circle cx="10" cy="17" r="1"/>
                    </svg>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">DDJJ Asignaciones</h2>
                        <p class="text-sm text-gray-600">Declaración jurada para asignaciones familiares</p>
                    </div>
                </div>
                <a href="{{ route('asignaciones.familiares') }}" 
                   class="inline-flex items-center px-6 py-2 bg-[#77BF43] text-white font-semibold rounded-lg hover:bg-[#8cc4d8] transition-colors shadow-md">
                    Ir a DDJJ
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Botón volver -->
        <div class="mt-8 flex justify-center">
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