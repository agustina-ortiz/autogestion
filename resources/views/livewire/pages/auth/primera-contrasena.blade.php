<?php

use App\Mail\PerfilActualizado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Validate('required|string|min:6')]
    public string $password = '';

    #[Validate('required|string|same:password')]
    public string $password_confirmation = '';

    public function mount(): void
    {
        // Permitir acceso en dos casos: primer login (DNI en sesión) o usuario ya autenticado
        if (!session()->has('first_login_dni') && !Auth::check()) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    public function updatePassword(): void
    {
        $this->validate();

        if (session()->has('first_login_dni')) {
            // Flujo de primer login
            $dni = session('first_login_dni');
            $empleado = \App\Models\Maestro::where('DNI', $dni)->first();

            if (!$empleado) {
                session()->forget('first_login_dni');
                $this->redirect(route('login'), navigate: true);
                return;
            }

            $empleado->CLAVEWEB = $this->password;
            $empleado->save();

            session()->forget('first_login_dni');
            Auth::loginUsingId($empleado->id, true);
            session()->regenerate();
        } else {
            // Usuario ya autenticado cambiando su contraseña
            $empleado = Auth::user();
            $empleado->CLAVEWEB = $this->password;
            $empleado->save();

            try {
                Mail::to(config('mail.perfil_notificacion_to'))->send(new PerfilActualizado(
                    nombre: $empleado->NOMBRE ?? '',
                    legajo: (string) $empleado->LEGAJO,
                    motivo: 'Cambio de contraseña',
                    mensaje: 'El empleado modificó su contraseña desde autogestión.',
                ));
            } catch (\Exception $e) {
                \Log::error('Error al enviar mail de notificación de contraseña: ' . $e->getMessage(), [
                    'legajo' => $empleado->LEGAJO ?? null,
                ]);
            }
        }

        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-[#e8f5df] to-[#f4f9e8] py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-4">
        {{-- Header --}}
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-[#77BF43] rounded-full flex items-center justify-center shadow-[0_2px_8px_rgba(119,191,67,0.3)]">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-800 uppercase tracking-tight">
                Crear Nueva
            </h2>
            <h2 class="text-2xl font-bold text-[#77BF43] uppercase tracking-tight">
                Contraseña
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Por favor, crea una contraseña para futuros accesos
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] p-6 space-y-4 border border-gray-100">
            <form wire:submit="updatePassword" class="space-y-4">
                {{-- Información --}}
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-xs text-gray-700 leading-relaxed">
                            Esta es tu primera vez en el sistema. Debes crear una contraseña que usarás para futuros accesos. La contraseña debe tener al menos 6 caracteres.
                        </p>
                    </div>
                </div>

                {{-- Nueva Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nueva Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="password" 
                            id="password" 
                            type="password" 
                            required 
                            autofocus
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition duration-200 placeholder-gray-400"
                            placeholder="Ingrese su nueva contraseña"
                        />
                    </div>
                    @error('password') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirmar Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="password_confirmation" 
                            id="password_confirmation" 
                            type="password" 
                            required
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition duration-200 placeholder-gray-400"
                            placeholder="Confirme su contraseña"
                        />
                    </div>
                    @error('password_confirmation') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Requisitos de Contraseña --}}
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Requisitos de la contraseña:</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li class="flex items-center gap-1">
                            <svg class="h-3 w-3 text-[#77BF43]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Mínimo 6 caracteres
                        </li>
                        <li class="flex items-center gap-1">
                            <svg class="h-3 w-3 text-[#77BF43]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Las contraseñas deben coincidir
                        </li>
                    </ul>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button 
                        type="submit"
                        class="w-full flex justify-center items-center py-2.5 px-4 border-0 rounded-lg shadow-[0_2px_4px_rgba(119,191,67,0.3)] text-sm font-bold text-white bg-[#77BF43] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#77BF43] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] uppercase tracking-wide cursor-pointer"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Guardar Contraseña
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-600">
            2025 Subdirección de Recursos Humanos
        </p>
    </div>
</div>