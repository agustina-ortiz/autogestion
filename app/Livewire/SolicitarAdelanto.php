<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SolicitarAdelanto extends Component
{
    // Propiedades públicas
    public $mesActual;
    public $anioActual;
    public $fechaActual;
    public $fechaInicial;
    public $fechaLimite;
    public $montoMaximoAdelanto;
    public $puedesolicitarAdelanto = false;
    
    // Propiedades del formulario
    public $montoSolicitado;
    public $observaciones;

    public function mount()
    {
        $this->inicializarFechas();
        $this->verificarPeriodoHabilitado();
    }

    /**
     * Inicializa las fechas dinámicas
     */
    private function inicializarFechas()
    {
        $hoy = Carbon::now();
        
        // Mes y año actual
        $this->mesActual = $hoy->locale('es')->translatedFormat('F');
        $this->anioActual = $hoy->year;
        
        // Fecha actual formateada
        $this->fechaActual = $hoy->format('d/m/Y');
        
        // Fecha inicial (01 del mes actual)
        $this->fechaInicial = $hoy->copy()->startOfMonth()->format('d/m/Y');
        
        // Fecha límite (06 del mes actual)
        $this->fechaLimite = $hoy->copy()->startOfMonth()->addDays(5)->format('d/m/Y');
        
        // Monto máximo para adelantos
        $this->montoMaximoAdelanto = 250000.00;
    }

    /**
     * Verifica si el usuario está en el período habilitado para solicitar adelantos
     */
    private function verificarPeriodoHabilitado()
    {
        $hoy = Carbon::now();
        $diaActual = $hoy->day;
        
        // Verificar si está entre el día 1 y 6
        if ($diaActual >= 1 && $diaActual <= 6) {
            // Verificar si ya tiene un adelanto pendiente o aprobado este mes
            $adelantoPendiente = $this->tieneAdelantoPendiente();
            
            if (!$adelantoPendiente) {
                $this->puedesolicitarAdelanto = true;
            }
        }
    }

    /**
     * Verifica si el usuario ya tiene un adelanto pendiente o aprobado este mes
     */
    private function tieneAdelantoPendiente()
    {
        try {
            $userId = Auth::id();
            $hoy = Carbon::now();
            
            $adelanto = DB::table('solicitudes')
                ->where('user_id', $userId)
                ->where('tipo', 'Adelanto')
                ->whereIn('estado', ['Pendiente', 'Aprobado'])
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            return $adelanto !== null;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Valida el monto solicitado
     */
    private function validarMonto()
    {
        if (empty($this->montoSolicitado) || $this->montoSolicitado <= 0) {
            session()->flash('error', 'Debe ingresar un monto válido mayor a $0.');
            return false;
        }

        if ($this->montoSolicitado > $this->montoMaximoAdelanto) {
            session()->flash('error', 'El monto solicitado no puede superar $' . number_format($this->montoMaximoAdelanto, 2, ',', '.'));
            return false;
        }

        return true;
    }

    /**
     * Confirma la solicitud de adelanto
     */
    public function confirmarSolicitud()
    {
        // Verificar nuevamente que esté en período habilitado
        if (!$this->puedesolicitarAdelanto) {
            session()->flash('error', 'No se encuentra en el período habilitado para solicitar adelantos.');
            return;
        }

        // Validar el monto
        if (!$this->validarMonto()) {
            return;
        }

        // Verificar nuevamente que no tenga solicitud pendiente
        if ($this->tieneAdelantoPendiente()) {
            session()->flash('error', 'Ya tiene una solicitud de adelanto pendiente o aprobada para este mes.');
            return;
        }

        try {
            $userId = Auth::id();
            
            // Insertar la solicitud en la base de datos
            DB::table('solicitudes')->insert([
                'user_id' => $userId,
                'tipo' => 'Adelanto',
                'fecha_solicitud' => Carbon::now(),
                'estado' => 'Pendiente',
                'monto' => $this->montoSolicitado,
                'observaciones' => $this->observaciones ?? 'Solicitud de adelanto del mes de ' . $this->mesActual . ' ' . $this->anioActual,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            session()->flash('success', '¡Solicitud de adelanto enviada correctamente por $' . number_format($this->montoSolicitado, 2, ',', '.') . '! Será procesada en breve.');
            
            // Limpiar el formulario
            $this->montoSolicitado = null;
            $this->observaciones = null;
            
            // Actualizar el estado
            $this->puedesolicitarAdelanto = false;
            
            // Redirigir después de 2 segundos
            $this->dispatch('solicitud-enviada');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.solicitar-adelanto')
            ->layout('layouts.app');
    }
}