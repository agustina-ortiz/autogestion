<div class="min-h-screen bg-gradient-to-br from-[#91D5E2]/60 via-white to-[#BED630]/30 backdrop-blur-sm">
    <div class="container mx-auto px-6 py-10">

        {{-- Header con nombre de usuario --}}
        <div class="mb-8">
            <div class="relative bg-white/70 backdrop-blur-lg shadow-lg border border-white/50 rounded-2xl px-8 py-4">
                <div class="absolute -top-4 -left-4 bg-[#77BF43] text-white px-3 py-1 text-xs font-semibold rounded-md shadow-md">
                    Panel de Usuario
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Mis Compensatorios</h1>
                <p class="text-gray-600 mt-3 text-lg">
                    Bienvenido/a, 
                    <span class="font-semibold text-[#77BF43]">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>

        {{-- Mensaje de error --}}
        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabla de compensatorios --}}
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden border border-gray-100/60">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] px-8 py-5 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-white opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M4 6h16M4 6v14a2 2 0 002 2h12a2 2 0 002-2V6" />
                    </svg>
                    Lista de Compensatorios
                </h2>
                <div class="text-sm text-white/80">
                    {{ $rows->total() }} registros
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-[#91D5E2]/30 text-gray-700 uppercase text-xs font-semibold tracking-wide">
                        <tr>
                            <th class="px-6 py-4 text-center">#</th>
                            <th class="px-6 py-4 text-center">Fecha</th>
                            <th class="px-6 py-4 text-left">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/50">
                        @forelse ($rows as $index => $row)
                            <tr class="hover:bg-[#91D5E2]/10 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-medium">{{ $rows->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-center">{{ \Carbon\Carbon::parse($row->FECINASI)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-left">{{ $row->observaciones }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    No hay compensatorios disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Subtítulo debajo de la tabla --}}
            <div class="px-6 py-4 text-gray-700 font-semibold">
                Compensatorios Disponibles: {{ $dias }}
            </div>

            {{-- Paginación Livewire --}}
            <div class="px-6 py-4 border-t border-gray-200/70">
                {{ $rows->links() }}
            </div>

        </div>
    </div>
</div>
