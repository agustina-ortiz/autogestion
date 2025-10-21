<?php

use Illuminate\Support\Facades\Route;

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

require __DIR__.'/auth.php';
