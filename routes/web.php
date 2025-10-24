<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Recibos;
use App\Livewire\ReciboDetalle;
use App\Livewire\Asistencias;
use App\Livewire\Compensatorios;


// Ruta principal
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Ruta de logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Perfil
    Route::view('profile', 'profile')->name('profile');
    
    // Recibos (Livewire)
    Route::get('/recibos', Recibos::class)->name('recibos');
    
    // Detalle de recibo (Livewire)
    Route::get('/recibo/{numero}/{anio}/{mes}/{tipo}', ReciboDetalle::class)->name('recibo');
    
    // Asistencias (Livewire)
    Route::get('/asistencias', Asistencias::class)->name('asistencias');

    // Compensatorios (Livewire)
    Route::get('/compensatorios', Compensatorios::class)->name('compensatorios');
    
});

require __DIR__.'/auth.php';