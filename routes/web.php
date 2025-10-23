<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Recibos;
use App\Livewire\ReciboDetalle;

Route::view('/', 'welcome');

Route::get('/', function () {
    if (auth()->check()) {
        // Si está logueado, mostrar la pantalla de inicio
        return view('welcome');
    } else {
        // Si no está logueado, ir al login
        return redirect()->route('login');
    }
});

Route::post('/logout', function () {
    Auth::logout();                 // Cierra la sesión del usuario
    request()->session()->invalidate();  // Invalida la sesión
    request()->session()->regenerateToken(); // Regenera el token CSRF
    return redirect()->route('login');  // Redirige al login
})->name('logout');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('recibos', \App\Livewire\Recibos::class)
    ->middleware(['auth'])
    ->name('recibos');

Route::middleware(['auth'])->group(function () {
    Route::get('/recibos', Recibos::class)->name('recibos');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/recibos', Recibos::class)->name('recibos');
    Route::get('/recibo/{numero}/{anio}/{mes}/{tipo}', [Recibos::class, 'mostrarRecibo'])->name('recibo');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/recibos', Recibos::class)->name('recibos');
    Route::get('/recibo/{numero}/{anio}/{mes}/{tipo}', ReciboDetalle::class)->name('recibo');
});

require __DIR__.'/auth.php';
