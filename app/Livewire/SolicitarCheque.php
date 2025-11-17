<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\TipoMovimiento;
use Carbon\Carbon;

class SolicitarCheque extends Component
{
    // Tipo de movimiento
    public $tipoMovimiento;
    
    // Propiedades públicas
    public $fechaActual;
    public $nombreCompleto;
    public $legajo;
    
    // Propiedades del formulario
    public $formaCobro = 'cheque';

    public function mount()
    {
        $this->inicializarDatos();
    }

    /**
     * Inicializa los datos del usuario y el tipo de movimiento
     */
    private function inicializarDatos()
    {
        $user = Auth::user();
        
        // Fecha actual formateada
        $this->fechaActual = Carbon::now()->format('d/m/Y');
        
        // Nombre completo del usuario
        $this->nombreCompleto = $user->NOMBRE ?? '';
        
        // Legajo del usuario
        $this->legajo = $user->LEGAJO ?? '';
        
        // Obtener el tipo de movimiento sueldo por cheque (ID 6)
        $this->tipoMovimiento = TipoMovimiento::find(TipoMovimiento::SUELDO_CHEQUE);
    }

    /**
     * Verifica si el usuario está en el período habilitado para solicitar cheque
     */
    private function verificarPeriodoHabilitado()
    {
        // Verificar que exista el tipo de movimiento
        if (!$this->tipoMovimiento) {
            return [
                'puede' => false,
                'mensaje' => 'El tipo de solicitud no está disponible en este momento.'
            ];
        }

        // Verificar si está en período habilitado usando el método del modelo
        if (!$this->tipoMovimiento->estaEnPeriodoHabilitado()) {
            $fechaLimite = $this->tipoMovimiento->getFechaHastaFormateada();
            return [
                'puede' => false,
                'mensaje' => 'La fecha tope para solicitar cheque era el ' . $fechaLimite . '.'
            ];
        }

        return ['puede' => true];
    }

    /**
     * Verifica si el usuario ya tiene una solicitud pendiente o lista este mes
     */
    private function tieneSolicitudPendiente()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            $solicitud = Solicitud::porLegajo($legajoUsuario)
                ->porTipoMovimiento(TipoMovimiento::SUELDO_CHEQUE)
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            return $solicitud !== null;
            
        } catch (\Exception $e) {
            return false;
        }
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

        // Verificar que no tenga solicitud pendiente
        if ($this->tieneSolicitudPendiente()) {
            session()->flash('error', 'Ya tiene una solicitud de sueldo por cheque pendiente o procesada para este mes.');
            return;
        }

        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            // Crear la solicitud usando el modelo
            Solicitud::create([
                'legajo' => $legajoUsuario,
                'fecha_solicitud' => now(),
                'tipo_movimiento' => TipoMovimiento::SUELDO_CHEQUE,
                'origen' => Solicitud::ORIGEN_AUTOGESTION,
                'estado' => Solicitud::ESTADO_PENDIENTE,
                'forma_pago' => 'cheque', // Obligatorio para este tipo
                'importe' => null, // No aplica para sueldo por cheque
            ]);
            
            $mesNombre = $hoy->locale('es')->translatedFormat('F');

            // Mensaje de éxito
            session()->flash('success', '¡Solicitud enviada correctamente! Su sueldo del mes de ' . $mesNombre . ' será abonado por CHEQUE.');

            // Redirigir a la vista de solicitudes
            return redirect()->route('solicitudes');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.solicitar-cheque')
            ->layout('components.layouts.autogestion');
    }
}