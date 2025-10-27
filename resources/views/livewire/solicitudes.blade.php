<div class="min-h-screen">
    {{-- Header con nombre de usuario --}}
    <div class="mb-4">
        <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] rounded-xl px-6 py-3 shadow-lg backdrop-blur-xl border border-white/20 transform hover:scale-[1.01] transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2 drop-shadow-lg">
                        <span class="tracking-tight">Compensatorios</span>
                    </h1>
                </div>
                <p class="text-white/90 text-sm font-medium">
                    Bienvenido/a, 
                    <span class="font-bold drop-shadow-md">{{ Auth::user()->NOMBRE }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Mensaje de éxito --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- Mensaje de error --}}
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Secciones de Adelantos y Sueldos por Cheque --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        {{-- Sección ADELANTOS --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6">
                <h2 class="text-xl font-bold m-0 uppercase">Adelantos</h2>
            </div>
            <div class="p-6">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        <span class="font-bold text-yellow-700">ATENCIÓN!</span> Se le informa que los adelantos correspondientes al mes de <span class="font-semibold">{{ $mesActual }}</span> del año <span class="font-semibold">{{ $anioActual }}</span> deberán solicitarse entre el día <span class="font-semibold">{{ $fechaDesdeAdelantos }}</span> y el <span class="font-semibold">{{ $fechaHastaAdelantos }}</span> y no podrán superar el valor de <span class="font-semibold">${{ number_format($montoMaximoAdelanto, 2, ',', '.') }}</span>.
                    </p>
                    <p class="text-sm text-gray-700 mt-2">
                        Serán depositados en el transcurso del día <span class="font-semibold">{{ $fechaDepositoAdelantos }}</span>.
                    </p>
                </div>
                <a 
                    href="{{ route('solicitudes.adelanto') }}" 
                    class="w-1/2 bg-gradient-to-r from-[#77BF43] to-[#5da832] text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0 block text-center">
                    Solicitar Adelanto
                </a>
            </div>
        </div>

        {{-- Sección SUELDOS POR CHEQUE --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white p-4 px-6">
                <h2 class="text-xl font-bold m-0 uppercase">Sueldos por Cheque</h2>
            </div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded flex-grow">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        <span class="font-bold text-blue-700">IMPORTANTE!</span> Se le informa que la fecha tope para solicitar que el sueldo correspondiente al mes de <span class="font-semibold">{{ $mesActual }}</span> del año <span class="font-semibold">{{ $anioActual }}</span> sea abonado por <span class="font-semibold">CHEQUE</span> es <span class="font-semibold">{{ $fechaTopeCheque }}</span>.
                    </p>
                    <p class="text-sm text-gray-700 mt-2">
                        Caso contrario, se depositará en su cuenta sueldo del <span class="font-semibold">BANCO PROVINCIA</span>.
                    </p>
                </div>
                <a 
                    href="{{ route('solicitudes.cheque') }}" 
                    class="w-1/2 bg-gradient-to-r from-[#77BF43] to-[#5da832] text-white px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300 hover:from-[#5da832] hover:to-[#77BF43] hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(119,191,67,0.3)] hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] border-0 mt-auto block text-center">
                    Solicitar Sueldo por Cheque
                </a>
            </div>
        </div>
    </div>

    {{-- Tabla de solicitudes --}}
    <div class="mb-8">
        <h2 class="text-[#77BF43] text-2xl font-bold mb-4 uppercase">
            Mis Solicitudes
        </h2>

        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gradient-to-r from-[#77BF43] to-[#BED630] text-white">
                        <tr>
                            <th class="p-4 text-left font-semibold text-sm">#</th>
                            <th class="p-4 text-left font-semibold text-sm">Tipo</th>
                            <th class="p-4 text-left font-semibold text-sm">Fecha Solicitud</th>
                            <th class="p-4 text-left font-semibold text-sm">Estado</th>
                            <th class="p-4 text-left font-semibold text-sm">Monto</th>
                            <th class="p-4 text-left font-semibold text-sm">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($solicitudes) > 0)
                            @php $i = 1; @endphp
                            @foreach ($solicitudes as $solicitud)
                                <tr class="border-b border-[#e5e7eb] hover:bg-[#f9fafb]">
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">{{ $i }}</td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            {{ $solicitud->tipo === 'Adelanto' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $solicitud->tipo }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($solicitud->estado === 'Pendiente') bg-orange-100 text-orange-800
                                            @elseif($solicitud->estado === 'Aprobado') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        @if($solicitud->monto)
                                            ${{ number_format($solicitud->monto, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-left text-sm text-[#374151]">
                                        {{ $solicitud->observaciones ?? '-' }}
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="p-12 text-center text-[#999] text-lg">
                                    No tienes solicitudes registradas
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Botón Volver --}}
    <div class="flex justify-center">
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