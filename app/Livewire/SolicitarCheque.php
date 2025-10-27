<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SolicitarCheque extends Component
{
    // Propiedades públicas
    public $fechaActual;
    public $nombreCompleto;
    public $legajo;
    
    // Propiedades del formulario
    public $formaCobro = '';
    public $observaciones;

    public function mount()
    {
        $this->inicializarDatos();
    }

    /**
     * Inicializa los datos del usuario y la fecha
     */
    private function inicializarDatos()
    {
        $user = Auth::user();
        
        // Fecha actual formateada
        $this->fechaActual = Carbon::now()->format('d/m/Y');
        
        // Nombre completo del usuario
        $this->nombreCompleto = $user->NOMBRE ?? '';
        
        // Legajo del usuario
        $this->legajo = $user->LEGAJO ?? $user->legajo ?? '';
    }

    /**
     * Verifica si el usuario está en el período habilitado para solicitar cheque
     */
    private function verificarPeriodoHabilitado()
    {
        $hoy = Carbon::now();
        $diaActual = $hoy->day;
        
        // Solo se puede solicitar hasta el día 27
        if ($diaActual > 27) {
            return [
                'puede' => false,
                'mensaje' => 'La fecha tope para solicitar cheque era el día 27 del mes.'
            ];
        }
        
        return ['puede' => true];
    }

    /**
     * Verifica si el usuario ya tiene una solicitud pendiente o aprobada este mes
     */
    private function tieneSolicitudPendiente()
    {
        try {
            $userId = Auth::id();
            $hoy = Carbon::now();
            
            $solicitud = DB::table('solicitudes')
                ->where('user_id', $userId)
                ->where('tipo', 'Cheque')
                ->whereIn('estado', ['Pendiente', 'Aprobado'])
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            return $solicitud !== null;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Valida el formulario
     */
    private function validarFormulario()
    {
        if (empty($this->formaCobro)) {
            session()->flash('error', 'Debe seleccionar una forma de cobro.');
            return false;
        }

        if (!in_array($this->formaCobro, ['deposito', 'cheque'])) {
            session()->flash('error', 'Forma de cobro inválida.');
            return false;
        }

        return true;
    }

    /**
     * Confirma la solicitud de forma de cobro
     */
    public function confirmarSolicitud()
    {
        // Verificar período habilitado
        $validacionPeriodo = $this->verificarPeriodoHabilitado();
        if (!$validacionPeriodo['puede']) {
            session()->flash('error', $validacionPeriodo['mensaje']);
            return;
        }

        // Validar formulario
        if (!$this->validarFormulario()) {
            return;
        }

        // Verificar que no tenga solicitud pendiente
        if ($this->tieneSolicitudPendiente()) {
            session()->flash('error', 'Ya tiene una solicitud de forma de cobro pendiente o aprobada para este mes.');
            return;
        }

        try {
            $userId = Auth::id();
            $hoy = Carbon::now();
            
            // Determinar el texto según la forma de cobro seleccionada
            $formaCobroTexto = $this->formaCobro === 'cheque' 
                ? 'Por Cheque' 
                : 'Por Depósito en cuenta sueldo';
            
            $observacionesFinales = $this->observaciones 
                ? $this->observaciones 
                : 'Solicitud de forma de cobro: ' . $formaCobroTexto;
            
            // Insertar la solicitud en la base de datos
            DB::table('solicitudes')->insert([
                'user_id' => $userId,
                'tipo' => 'Cheque',
                'fecha_solicitud' => Carbon::now(),
                'estado' => 'Pendiente',
                'monto' => null, // No aplica para forma de cobro
                'forma_cobro' => $this->formaCobro, // Nuevo campo para almacenar la forma de cobro
                'observaciones' => $observacionesFinales,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $mensajeExito = $this->formaCobro === 'cheque'
                ? '¡Solicitud enviada correctamente! Su sueldo del mes de ' . $hoy->locale('es')->translatedFormat('F') . ' será abonado por CHEQUE.'
                : '¡Solicitud enviada correctamente! Su sueldo del mes de ' . $hoy->locale('es')->translatedFormat('F') . ' será depositado en su cuenta sueldo del Banco Provincia.';
            
            session()->flash('success', $mensajeExito);
            
            // Limpiar el formulario
            $this->formaCobro = '';
            $this->observaciones = null;
            
            // Redirigir después de 2 segundos
            $this->dispatch('solicitud-enviada');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.solicitar-cheque')
            ->layout('layouts.app');
    }
}