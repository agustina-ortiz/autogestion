<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\TipoMovimiento;
use Carbon\Carbon;

class Solicitudes extends Component
{
    // Propiedades para adelantos
    public $tipoAdelanto;
    public $fechaDesdeAdelantos;
    public $fechaHastaAdelantos;
    public $montoMaximoAdelanto;
    public $fechaDepositoAdelantos;
    
    // Propiedades para cheques
    public $tipoCheque;
    public $fechaTopeCheque;
    
    // Propiedades generales
    public $mesActual;
    public $anioActual;
    public $solicitudes = [];

    public function mount()
    {
        // Inicializar fechas y mes
        $this->inicializarDatos();
        
        // Cargar las solicitudes del usuario
        $this->cargarSolicitudes();
    }

    /**
     * Inicializa los datos desde in_tipo_movimiento
     */
    private function inicializarDatos()
    {
        $hoy = Carbon::now();
        
        // Mes y año actual
        $this->anioActual = $hoy->year;
        $this->mesActual = strtoupper($hoy->locale('es')->translatedFormat('F'));

        // Obtener tipo adelanto de sueldo (ID 5)
        $this->tipoAdelanto = TipoMovimiento::find(TipoMovimiento::ADELANTO_SUELDO);
        
        if ($this->tipoAdelanto) {
            $this->fechaDesdeAdelantos = $this->tipoAdelanto->getFechaDesdeFormateada();
            $this->fechaHastaAdelantos = $this->tipoAdelanto->getFechaHastaFormateada();
            $this->fechaDepositoAdelantos = $this->tipoAdelanto->getFechaAcreditacionFormateada();
            $this->montoMaximoAdelanto = $this->tipoAdelanto->importe_maximo ?? 250000.00;
        } else {
            // Valores por defecto si no existe el tipo
            $this->fechaDesdeAdelantos = $hoy->copy()->startOfMonth()->format('d/m/Y');
            $this->fechaHastaAdelantos = $hoy->copy()->startOfMonth()->addDays(6)->format('d/m/Y');
            $this->fechaDepositoAdelantos = $hoy->copy()->startOfMonth()->addDays(9)->format('d/m/Y');
            $this->montoMaximoAdelanto = 250000.00;
        }

        // Obtener tipo sueldo por cheque (ID 6)
        $this->tipoCheque = TipoMovimiento::find(TipoMovimiento::SUELDO_CHEQUE);
        
        if ($this->tipoCheque) {
            $this->fechaTopeCheque = $this->tipoCheque->getFechaHastaFormateada();
        } else {
            // Valor por defecto si no existe el tipo
            $this->fechaTopeCheque = $hoy->copy()->startOfMonth()->addDays(26)->format('d/m/Y');
        }
    }

    /**
     * Carga las solicitudes del usuario desde la base de datos
     */
    private function cargarSolicitudes()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            
            // Obtener todas las solicitudes del usuario ordenadas por fecha
            $this->solicitudes = Solicitud::porLegajo($legajoUsuario)
                ->with('tipoMovimiento')
                ->masRecientes()
                ->get()
                ->map(function ($solicitud) {
                    return (object)[
                        'id' => $solicitud->id,
                        'tipo' => $solicitud->tipoMovimiento->tipo_movimiento ?? 'Desconocido',
                        'fecha_solicitud' => $solicitud->fecha_solicitud->format('Y-m-d H:i:s'),
                        'estado' => $solicitud->getNombreEstado(),
                        'monto' => $solicitud->importe,
                        'observaciones' => $solicitud->forma_pago === 'cheque' 
                            ? 'Forma de pago: Cheque' 
                            : 'Forma de pago: Depósito',
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