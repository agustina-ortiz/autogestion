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
use App\Livewire\Planillas;
use App\Livewire\AsignacionesFamiliares;
use App\Livewire\Hijos;
use App\Livewire\PreguntasFrecuentes;
use App\Livewire\Perfil;
use App\Http\Controllers\ReciboPDFController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\PerfilFotoController;
use App\Http\Controllers\AsignacionesFamiliaresController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PushController;
use Livewire\Volt\Volt;

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

Volt::route('/primera-contrasena', 'pages.auth.primera-contrasena')
    ->middleware('guest')
    ->name('primera.contrasena');

Route::get('/reloj', App\Livewire\Reloj::class)->name('reloj');

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/noticia/{id}', [DashboardController::class, 'verNoticia'])->name('noticia.ver');
    
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
    
    // Ver Anticipo
    Route::get('/anticipo', AnticipoJubilatorio::class)->name('anticipo.jubilatorio');

    // Detalle de Anticipo
    Route::get('/anticipo/{anio}/{mes}/{tipo}/{sub}', AnticipoJubilatorioDetalle::class)->name('anticipo.detalle');

    // Hijos
    Route::get('/hijos', Hijos::class)->name('hijos');

    // Planillas
    Route::get('/planillas', Planillas::class)->name('planillas');

    Route::post('/planilla/subir', [PlanillaController::class, 'subir'])->name('planilla.subir');

    Route::get('/planilla/descargar/{dni}/{nombre}', function($dni, $nombre) {
        $planillasComponent = new App\Livewire\Planillas();
        return $planillasComponent->descargarPlanilla($dni, $nombre);
    })->name('planilla.descargar');

    // Asignaciones Familiares
    Route::get('/asignaciones-familiares', AsignacionesFamiliares::class)->name('asignaciones.familiares');

    // Preguntas Frecuentes
    Route::get('/preguntas-frecuentes', PreguntasFrecuentes::class)->name('preguntas.frecuentes');
    Route::get('/perfil', Perfil::class)->name('perfil');

    // Ruta para generar el PDF del recibo
    Route::get('/recibo/pdf/{nroRecibo}/{anio}/{mes}/{tipoLiq}', [ReciboPDFController::class, 'generarPDF'])
        ->name('recibo.pdf')
        ->middleware('auth');

    Route::post('/perfil/foto/subir', [PerfilFotoController::class, 'subirFoto'])
        ->name('perfil.foto.subir');

    Route::post('/asignaciones-familiares/subir-archivo', [AsignacionesFamiliaresController::class, 'subirArchivo'])
        ->name('asignaciones-familiares.subir-archivo');
    
    Route::post('/asignaciones-familiares/eliminar-archivo', [AsignacionesFamiliaresController::class, 'eliminarArchivo'])
        ->name('asignaciones-familiares.eliminar-archivo');

    // Push Notifications
    Route::post('/push/subscribe', [PushController::class, 'subscribe'])->name('push.subscribe');
});

});

require __DIR__.'/auth.php';