<?php

use App\Livewire\AnticipoJubilatorio;
use App\Livewire\AnticipoJubilatorioDetalle;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Recibos;
use App\Livewire\ReciboDetalle;
use App\Livewire\Asistencias;
use App\Livewire\Compensatorios;
use App\Livewire\Solicitudes;
use App\Livewire\SolicitarAdelanto;
use App\Livewire\SolicitarCheque;
use App\Livewire\Planillas;


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

    // Solicitudes (Livewire)
    Route::middleware('auth')->group(function () {
    Route::get('/solicitudes', Solicitudes::class)->name('solicitudes');
    Route::get('/solicitudes/adelanto', SolicitarAdelanto::class)->name('solicitudes.adelanto');
    Route::get('/solicitudes/cheque', SolicitarCheque::class)->name('solicitudes.cheque');

    // Ver Anticipo
    Route::get('/anticipo', AnticipoJubilatorio::class)->name('anticipo.jubilatorio');

    // Detalle de Anticipo
    Route::get('/anticipo/{anio}/{mes}/{tipo}/{sub}', AnticipoJubilatorioDetalle::class)->name('anticipo.detalle');

    Route::get('/planillas', Planillas::class)->name('planillas');

});
    
});

require __DIR__.'/auth.php';