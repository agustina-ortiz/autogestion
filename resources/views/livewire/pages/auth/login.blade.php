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

    public bool $showPasswordHelp = false;

    public bool $showFirstTimeHelp = false;

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

<div class="max-h-screen flex items-center justify-center bg-gradient-to-r from-[#e8f5df] to-[#f4f9e8] py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-4">
        {{-- Header --}}
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-[#77BF43] rounded-full flex items-center justify-center shadow-[0_2px_8px_rgba(119,191,67,0.3)]">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-800 uppercase tracking-tight">
                Autogestión
            </h2>
            <h2 class="text-2xl font-bold text-[#77BF43] uppercase tracking-tight">
                Recursos Humanos
            </h2>
            <p class="mt-1 text-xs text-gray-600">
                Ingrese sus credenciales para acceder
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.1)] p-6 space-y-4 border border-gray-100">
            <form wire:submit="login" class="space-y-4">
                {{-- DNI Field --}}
                <div>
                    <label for="dni" class="block text-sm font-semibold text-gray-700 mb-2">
                        DNI
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="dni" 
                            id="dni" 
                            type="text" 
                            required 
                            autofocus
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition duration-200 placeholder-gray-400"
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

                {{-- CLAVEWEB Field --}}
                <div>
                    <label for="claveweb" class="block text-sm font-semibold text-gray-700 mb-2">
                        Clave Web
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model="claveweb" 
                            id="claveweb" 
                            type="password" 
                            required
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-transparent transition duration-200 placeholder-gray-400"
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

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input 
                        wire:model="remember" 
                        id="remember" 
                        type="checkbox"
                        class="h-4 w-4 text-[#77BF43] focus:ring-[#77BF43] border-gray-300 rounded cursor-pointer"
                    />
                    <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">
                        Recordarme
                    </label>
                </div>

                {{-- First Time Access Help --}}
                <div class="pt-1 flex justify-center">
                    <button 
                        type="button"
                        wire:click="$toggle('showFirstTimeHelp')"
                        class="text-xs text-gray-600 hover:text-[#77BF43] transition-colors duration-200 flex items-center gap-1"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Primera vez que accedo
                        <svg 
                            class="h-3.5 w-3.5 transition-transform duration-200 {{ $showFirstTimeHelp ? 'rotate-180' : '' }}" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- First Time Help Message --}}
                    <div 
                        x-data="{ show: @entangle('showFirstTimeHelp') }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200"
                        style="display: none;"
                    >
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                Si es la primera vez que accede, ingrese su número de legajo en la clave de acceso, y el sistema le solicitará que cree una clave para futuros accesos.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button 
                        type="submit"
                        class="w-full flex justify-center items-center py-2.5 px-4 border-0 rounded-lg shadow-[0_2px_4px_rgba(119,191,67,0.3)] text-sm font-bold text-white bg-[#77BF43] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#77BF43] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_8px_rgba(119,191,67,0.5)] uppercase tracking-wide cursor-pointer"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Acceder
                    </button>
                </div>
            </form>

            {{-- Forgot Password Toggle --}}
            <div class="pt-2 border-t border-gray-100">
                <button 
                    type="button"
                    wire:click="$toggle('showPasswordHelp')"
                    class="w-full text-sm text-gray-600 hover:text-[#77BF43] transition-colors duration-200 flex items-center justify-center gap-1"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    No recuerdo la clave
                    <svg 
                        class="h-4 w-4 transition-transform duration-200 {{ $showPasswordHelp ? 'rotate-180' : '' }}" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Help Message --}}
                <div 
                    x-data="{ show: @entangle('showPasswordHelp') }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mt-3 p-3 bg-[#e8f5df] rounded-lg border border-[#BED630]"
                    style="display: none;"
                >
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-[#77BF43] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-gray-700">
                            Deberá solicitar un blanqueo de la misma en la oficina de Recursos Humanos del municipio.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-600">
            © 2025 Subdirección de Recursos Humanos - Todos los derechos reservados
        </p>
    </div>
</div>