<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Solicitudes extends Component
{
    // Propiedades para las fechas y montos
    public $mesActual;
    public $anioActual;
    public $fechaDesdeAdelantos;
    public $fechaHastaAdelantos;
    public $montoMaximoAdelanto;
    public $fechaDepositoAdelantos;
    public $fechaTopeCheque;
    
    // Propiedades para las solicitudes
    public $solicitudes = [];

    public function mount()
    {
        // Inicializar las fechas
        $this->inicializarFechas();
        
        // Cargar las solicitudes del usuario
        $this->cargarSolicitudes();
    }

    /**
     * Inicializa las fechas dinámicas para adelantos y cheques
     */
    private function inicializarFechas()
    {
        try {
            $hoy = Carbon::now();
            
            // Mes y año actual
            $this->anioActual = $hoy->year;
            
            // Obtener el nombre del mes en español y en mayúsculas
            $mesNombre = strtoupper($hoy->locale('es')->translatedFormat('F'));
            $this->mesActual = $mesNombre;
            
            // Obtener el número del mes para verificar si es aguinaldo
            $mesNumero = $hoy->month;
            
            // Buscar los datos en la tabla in_datos usando el nombre del mes en mayúsculas
            $datos = DB::table('in_datos')
                ->where('ANIO', $this->anioActual)
                ->where('MES', $mesNombre)
                ->first();
            
            if ($datos) {
                // Verificar si es mes de aguinaldo (junio = 6 o diciembre = 12)
                $esAguinaldo = in_array($mesNumero, [6, 12]);
                
                if ($esAguinaldo) {
                    // Para meses de aguinaldo, usar FECDES3 y FECHAS3
                    $this->fechaDesdeAdelantos = $this->formatearFecha($datos->FECDES3);
                    $this->fechaHastaAdelantos = $this->formatearFecha($datos->FECHAS3);
                } else {
                    // Para meses normales, usar FECDES y FECHAS
                    $this->fechaDesdeAdelantos = $this->formatearFecha($datos->FECDES);
                    $this->fechaHastaAdelantos = $this->formatearFecha($datos->FECHAS);
                }
                
                // Fecha de depósito de adelantos
                $this->fechaDepositoAdelantos = $this->formatearFecha($datos->FECACR);
                
                // Monto máximo para adelantos
                $this->montoMaximoAdelanto = $datos->IMPORTE_MAX ?? 250000.00;
                
                // Fecha tope para cheque
                // Para aguinaldo usar FECHAS3, para normal usar FECHAS2
                if ($esAguinaldo) {
                    $this->fechaTopeCheque = $this->formatearFecha($datos->FECHAS3);
                } else {
                    $this->fechaTopeCheque = $this->formatearFecha($datos->FECHAS2);
                }
            } else {
                // Si no hay datos en la tabla, usar valores predeterminados
                $this->usarValoresPredeterminados();
            }
            
        } catch (\Exception $e) {
            // En caso de error, usar valores predeterminados
            $this->usarValoresPredeterminados();
            session()->flash('error', 'Error al cargar las fechas: ' . $e->getMessage());
        }
    }

    /**
     * Formatea una fecha de la base de datos al formato dd/mm/yyyy
     */
    private function formatearFecha($fecha)
    {
        if (!$fecha) {
            return '-';
        }

        try {
            // Si la fecha viene como string o como objeto DateTime/Carbon
            if (is_string($fecha)) {
                $carbon = Carbon::parse($fecha);
            } else {
                $carbon = Carbon::instance($fecha);
            }
            
            return $carbon->format('d/m/Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Establece valores predeterminados cuando no hay datos en la tabla
     */
    private function usarValoresPredeterminados()
    {
        $hoy = Carbon::now();
        
        $this->fechaDesdeAdelantos = $hoy->copy()->startOfMonth()->format('d/m/Y');
        $this->fechaHastaAdelantos = $hoy->copy()->startOfMonth()->addDays(5)->format('d/m/Y');
        $this->fechaDepositoAdelantos = $hoy->copy()->startOfMonth()->addDays(8)->format('d/m/Y');
        $this->montoMaximoAdelanto = 250000.00;
        $this->fechaTopeCheque = $hoy->copy()->startOfMonth()->addDays(26)->format('d/m/Y');
    }

    /**
     * Carga las solicitudes del usuario desde la base de datos
     */
    private function cargarSolicitudes()
    {
        try {
            $userId = Auth::id();
            
            $this->solicitudes = DB::table('solicitudes')
                ->where('user_id', $userId)
                ->orderBy('fecha_solicitud', 'desc')
                ->get()
                ->map(function ($solicitud) {
                    return (object)[
                        'id' => $solicitud->id,
                        'tipo' => $solicitud->tipo,
                        'fecha_solicitud' => $solicitud->fecha_solicitud,
                        'estado' => $solicitud->estado,
                        'monto' => $solicitud->monto ?? null,
                        'observaciones' => $solicitud->observaciones ?? null,
                    ];
                })
                ->toArray();
                
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar las solicitudes: ' . $e->getMessage());
            $this->solicitudes = [];
        }
    }

    public function render()
    {
        return view('livewire.solicitudes')
            ->layout('components.layouts.autogestion');
    }
}