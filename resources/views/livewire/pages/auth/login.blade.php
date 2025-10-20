<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Validate('required|string')]
    public string $dni = '';

    #[Validate('required|string')]
    public string $claveweb = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['DNI' => $this->dni, 'password' => $this->claveweb], $this->remember)) {
            session()->regenerate();
            $this->redirect(route('dashboard'), navigate: true);
        }

        $this->addError('dni', 'Las credenciales proporcionadas no son correctas.');
    }
}; ?>

<div class="max-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-50 via-teal-50 to-emerald-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-4">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-800 uppercase tracking-tight">
                Subdirección de
            </h2>
            <h2 class="text-2xl font-bold text-teal-700 uppercase tracking-tight">
                Recursos Humanos
            </h2>
            <p class="mt-1 text-xs text-gray-600">
                Ingrese sus credenciales para acceder
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 space-y-4 border border-gray-100">
            <form wire:submit="login" class="space-y-4">
                <!-- DNI Field -->
                <div>
                    <label for="dni" class="block text-sm font-semibold text-gray-700 mb-2">
                        DNI
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="dni" 
                            id="dni" 
                            type="text" 
                            required 
                            autofocus
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 placeholder-gray-400"
                            placeholder="Ingrese su DNI"
                        />
                    </div>
                    @error('dni') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- CLAVEWEB Field -->
                <div>
                    <label for="claveweb" class="block text-sm font-semibold text-gray-700 mb-2">
                        Clave Web
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="claveweb" 
                            id="claveweb" 
                            type="password" 
                            required
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 placeholder-gray-400"
                            placeholder="Ingrese su contraseña"
                        />
                    </div>
                    @error('claveweb') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        wire:model="remember" 
                        id="remember" 
                        type="checkbox"
                        class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded cursor-pointer"
                    />
                    <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">
                        Recordarme
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit"
                        class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-lg text-sm font-bold text-white bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 transform hover:scale-105 uppercase tracking-wide"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Acceder
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-600">
            © 2025 Subdirección de Recursos Humanos - Todos los derechos reservados
        </p>
    </div>
</div>