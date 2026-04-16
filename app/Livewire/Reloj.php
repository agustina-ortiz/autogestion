<?php
// app/Livewire/Reloj.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;

class Reloj extends Component
{
    public $tarjeta = '0';
    public $resultado = '';
    public $mostrarResultado = false;
    public $notificaciones = [];
    public $fecha;
    
    public function mount()
    {
        $this->fecha = Carbon::now('America/Argentina/Buenos_Aires')->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $this->cargarNotificaciones();
    }
    
    private function obtenerFechaActual()
    {
        return Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');
    }
    
    private function obtenerHoraActual()
    {
        return Carbon::now('America/Argentina/Buenos_Aires')->format('H:i:s');
    }
    
    public function cargarNotificaciones()
    {
        try {
            $fecha = $this->obtenerFechaActual();
            
            $this->notificaciones = DB::connection('mysql')
                ->table('in_noticia')
                ->select('*')
                ->where('FECHA', '<=', $fecha)
                ->where('FECHAVTO', '>', $fecha)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Error al cargar notificaciones: ' . $e->getMessage());
            $this->notificaciones = collect();
        }
    }
    
    public function fichar($numeroTarjeta, $fotoBase64 = null)
    {
        try {
            // Usamos el parámetro directamente
            $this->tarjeta = $numeroTarjeta; 
            
            $fecha = $this->obtenerFechaActual();
            $hora = $this->obtenerHoraActual();
            
            $tarjetaOriginal = $this->tarjeta;
            $tarjeta = intdiv((int)$this->tarjeta, 10);
            
            if ($tarjeta < 1) {
                $this->resultado = '
                    <div class="flex items-center gap-6 p-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h1 class="text-danger text-3xl font-bold mb-2">Tarjeta Inválida</h1>
                            <p class="text-gray-600 text-lg">No se pudo leer la tarjeta correctamente</p>
                        </div>
                    </div>';
                $this->mostrarResultado = true;
                $this->resetTarjeta();
                return;
            }
            
            $feriado = DB::connection('mysql')
                ->table('in_feriados')
                ->where('FECHA', '=', $fecha)
                ->exists();
            
            $diaSemana = Carbon::now('America/Argentina/Buenos_Aires')->dayOfWeek;
            $dia = $diaSemana == 0 ? 1 : $diaSemana + 1;
            
            $legajo = DB::connection('mysql')
                ->table('in_maestro as m')
                ->select(
                    'm.LEGAJO as legajo',
                    'm.NOMBRE as nombre',
                    'm.TARJETA as tarjeta',
                    't.TOPE as tope'
                )
                ->leftJoin('in_topes as t', 't.CODTAR', '=', 'm.TARJETA')
                ->where('m.TARJETA', '=', $tarjeta)
                ->first();
            
            if (!$legajo) {
                $this->resultado = '
                    <div class="flex items-center gap-6 p-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-x-fill text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h1 class="text-danger text-3xl font-bold mb-2">Tarjeta No Reconocida</h1>
                            <p class="text-gray-600 text-lg mb-2">Consulte con Recursos Humanos</p>
                            <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">Tarjeta: ' . $tarjeta . '</span>
                        </div>
                    </div>';
                $this->mostrarResultado = true;
                $this->resetTarjeta();
                return;
            }
            
            $legajo->dia = $dia;
            
            $tieneInasistencia = DB::connection('mysql')
                ->table('in_movimie')
                ->where('FECINASI', '=', $fecha)
                ->where('LEGAJO', '=', $legajo->legajo)
                ->exists();
            
            if ($tieneInasistencia) {
                $this->resultado = '
                    <div class="flex items-center gap-6 p-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h1 class="text-warning text-3xl font-bold mb-2">Fichada Bloqueada</h1>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-1">' . $legajo->nombre . '</h2>
                            <p class="text-gray-600 text-lg">Tiene una novedad registrada. Consulte con RRHH.</p>
                        </div>
                    </div>';
                $this->mostrarResultado = true;
                $this->resetTarjeta();
                return;
            }
            
            $fichada = DB::connection('mysql')
                ->table('in_horas')
                ->where('FECHA', '=', $fecha)
                ->where('CODTAR', '=', $legajo->tarjeta)
                ->orderBy('HORA', 'desc')
                ->limit(1)
                ->get();
            
            $mensaje1 = $this->obtenerMensajeSaludo($hora);
            
            if ($legajo->dia != 1 && $legajo->dia != 7 && !$feriado) {
                if ($legajo->tope && $legajo->tope < $hora && $fichada->count() == 0) {
                    $this->resultado = '
                        <div class="flex items-center gap-6 p-4">
                            <div class="flex-shrink-0">
                                <i class="bi bi-clock-history text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <h1 class="text-danger text-3xl font-bold mb-2">¡LLEGADA TARDE!</h1>
                                <h2 class="text-2xl font-semibold text-gray-800 mb-1">' . $legajo->nombre . '</h2>
                                <p class="text-gray-600 text-lg mb-1">Ha superado el horario límite de entrada</p>
                                <p class="text-gray-600 text-base">Tope: <strong>' . $legajo->tope . '</strong> | Hora actual: <strong>' . $hora . '</strong></p>
                            </div>
                        </div>';
                    $this->mostrarResultado = true;
                    $this->resetTarjeta();
                    return;
                }
            }
            
            if ($fichada->count() > 0) {
                $ultima_fic = $fichada->first();
                
                $fecha1 = new DateTime($fecha . ' ' . $ultima_fic->HORA);
                $fecha2 = new DateTime($fecha . ' ' . $hora);
                $intervalo = $fecha1->diff($fecha2);
                $dife = $intervalo->format('%i') + ($intervalo->format('%h') * 60);
                
                if ($dife <= 10) {
                    $this->resultado = '
                        <div class="flex items-center gap-6 p-4">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-info" style="font-size: 5rem;"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <h1 class="text-info text-3xl font-bold mb-2">Ya Registrado</h1>
                                <h2 class="text-2xl font-semibold text-gray-800 mb-1">' . $legajo->nombre . '</h2>
                                <p class="text-gray-600 text-lg mb-1">Su última fichada fue a las <strong>' . $ultima_fic->HORA . '</strong></p>
                                <p class="text-gray-500 text-base">Debe esperar al menos 10 minutos entre fichadas</p>
                            </div>
                        </div>';
                    $this->mostrarResultado = true;
                    $this->resetTarjeta();
                    return;
                }
            }
            
            try {
                $id = DB::connection('mysql')
                    ->table('in_horas')
                    ->insertGetId([
                        'CODTAR' => $tarjeta,
                        'FECHA' => $fecha,
                        'HORA' => $hora
                    ]);
                
                // LAYOUT HORIZONTAL: Ícono a la izquierda, información a la derecha
                $this->resultado = '
                    <div class="flex items-center gap-6 p-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h1 class="text-success text-3xl font-bold mb-2">¡Fichada Registrada!</h1>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-1">' . $legajo->nombre . '</h2>
                            <p class="text-gray-600 text-lg">' . $mensaje1 . '</p>
                        </div>
                    </div>';
                
                $this->mostrarResultado = true;
                
            } catch (\Exception $e) {
                Log::error('Error al insertar fichada: ' . $e->getMessage());
                $this->resultado = '
                    <div class="flex items-center gap-6 p-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h1 class="text-danger text-3xl font-bold mb-2">Error al Registrar</h1>
                            <p class="text-gray-600 text-lg">Por favor, intente nuevamente</p>
                        </div>
                    </div>';
                $this->mostrarResultado = true;
            }
            
        } catch (\Exception $e) {
            Log::error('Error en fichaje: ' . $e->getMessage());
            $this->resultado = '
                <div class="flex items-center gap-6 p-4">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <h1 class="text-danger text-3xl font-bold mb-2">Error del Sistema</h1>
                        <p class="text-gray-600 text-lg">Por favor, contacte al administrador</p>
                    </div>
                </div>';
            $this->mostrarResultado = true;
        }
        
        $this->resetTarjeta();
    }
    
    private function obtenerMensajeSaludo($hora)
    {
        $horaComparar = substr($hora, 0, 5);
        
        if ($horaComparar > "06:00" && $horaComparar <= "12:00") {
            return "Buenos días";
        } elseif ($horaComparar > "12:00" && $horaComparar <= "20:00") {
            return "Buenas tardes";
        } else {
            return "Buenas noches";
        }
    }
    
    private function resetTarjeta()
    {
        $this->tarjeta = '0';
    }
    
    public function limpiarResultado()
    {
        $this->mostrarResultado = false;
        $this->resultado = '';
        // Opcional: Despachar el evento para que el JS sepa que ya se limpió
        $this->dispatch('limpiarResultado');
    }
    
    public function render()
    {
        return view('livewire.reloj')
            ->layout('layouts.reloj');
    }
}