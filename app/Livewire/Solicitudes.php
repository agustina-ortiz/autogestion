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
        $hoy = Carbon::now();
        
        // Mes y año actual
        $this->mesActual = $hoy->locale('es')->translatedFormat('F'); // Nombre del mes en español
        $this->anioActual = $hoy->year;
        
        // Fechas para adelantos
        // Del 01 al 06 del mes actual
        $this->fechaDesdeAdelantos = $hoy->copy()->startOfMonth()->format('d/m/Y');
        $this->fechaHastaAdelantos = $hoy->copy()->startOfMonth()->addDays(5)->format('d/m/Y');
        
        // Depósito el día 09
        $this->fechaDepositoAdelantos = $hoy->copy()->startOfMonth()->addDays(8)->format('d/m/Y');
        
        // Monto máximo para adelantos
        $this->montoMaximoAdelanto = 250000.00;
        
        // Fecha tope para cheque: día 27
        $this->fechaTopeCheque = $hoy->copy()->startOfMonth()->addDays(26)->format('d/m/Y');
    }

    /**
     * Carga las solicitudes del usuario desde la base de datos
     */
    private function cargarSolicitudes()
    {
        try {
            $userId = Auth::id();
            
            // Ajusta esta consulta según tu estructura de base de datos
            // Este es un ejemplo genérico
            $this->solicitudes = DB::table('solicitudes')
                ->where('user_id', $userId)
                ->orderBy('fecha_solicitud', 'desc')
                ->get()
                ->map(function ($solicitud) {
                    return (object)[
                        'id' => $solicitud->id,
                        'tipo' => $solicitud->tipo, // 'Adelanto' o 'Cheque'
                        'fecha_solicitud' => $solicitud->fecha_solicitud,
                        'estado' => $solicitud->estado, // 'Pendiente', 'Aprobado', 'Rechazado'
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