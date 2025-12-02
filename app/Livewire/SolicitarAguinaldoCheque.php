<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\TipoMovimiento;
use Carbon\Carbon;

class SolicitarAguinaldoCheque extends Component
{
    // Tipo de movimiento
    public $tipoMovimiento;
    
    // Propiedades públicas
    public $fechaActual;
    public $nombreCompleto;
    public $legajo;
    public $mesActual;
    public $anioActual;
    public $fechaTopeAguinaldo;
    
    // Propiedades del formulario
    public $formaCobro = 'cheque';

    public function mount()
    {
        $this->inicializarDatos();
        $this->verificarMesHabilitado();
    }

    /**
     * Inicializa los datos del usuario y el tipo de movimiento
     */
    private function inicializarDatos()
    {
        $user = Auth::user();
        $hoy = Carbon::now();
        
        // Fecha actual formateada
        $this->fechaActual = $hoy->format('d/m/Y');
        
        // Mes y año actual
        $this->mesActual = $hoy->locale('es')->translatedFormat('F');
        $this->anioActual = $hoy->year;
        
        // Nombre completo del usuario
        $this->nombreCompleto = $user->NOMBRE ?? '';
        
        // Legajo del usuario
        $this->legajo = $user->LEGAJO ?? '';
        
        // Obtener el tipo de movimiento aguinaldo por cheque (ID 8)
        $this->tipoMovimiento = TipoMovimiento::find(TipoMovimiento::AGUINALDO_CHEQUE);
        
        if ($this->tipoMovimiento) {
            $this->fechaTopeAguinaldo = $this->tipoMovimiento->getFechaHastaFormateada();
        }
    }

    /**
     * Verifica si estamos en junio o diciembre
     */
    private function verificarMesHabilitado()
    {
        $mesActual = Carbon::now()->month;
        
        if (!in_array($mesActual, [6, 12])) {
            session()->flash('error', 'Las solicitudes de aguinaldo por cheque solo están disponibles en junio y diciembre.');
            return redirect()->route('solicitudes');
        }
    }

    /**
     * Verifica si el usuario está en el período habilitado para solicitar aguinaldo por cheque
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
                'mensaje' => 'La fecha tope para solicitar aguinaldo por cheque era el ' . $fechaLimite . '.'
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
                ->porTipoMovimiento(TipoMovimiento::AGUINALDO_CHEQUE)
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            return $solicitud !== null;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Confirma la solicitud de aguinaldo por cheque
     */
    public function confirmarSolicitud()
    {
        // Verificar nuevamente que estamos en junio o diciembre
        $mesActual = Carbon::now()->month;
        if (!in_array($mesActual, [6, 12])) {
            session()->flash('error', 'Las solicitudes de aguinaldo por cheque solo están disponibles en junio y diciembre.');
            $this->dispatch('scroll-to-error');
            return;
        }

        // Verificar período habilitado
        $validacionPeriodo = $this->verificarPeriodoHabilitado();
        if (!$validacionPeriodo['puede']) {
            session()->flash('error', $validacionPeriodo['mensaje']);
            $this->dispatch('scroll-to-error');
            return;
        }

        // Verificar que no tenga solicitud pendiente
        if ($this->tieneSolicitudPendiente()) {
            session()->flash('error', 'Ya tiene una solicitud de aguinaldo por cheque pendiente o procesada para este mes.');
            $this->dispatch('scroll-to-error');
            return;
        }

        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            // Crear la solicitud usando el modelo
            Solicitud::create([
                'legajo' => $legajoUsuario,
                'fecha_solicitud' => now(),
                'tipo_movimiento' => TipoMovimiento::AGUINALDO_CHEQUE,
                'origen' => Solicitud::ORIGEN_AUTOGESTION,
                'estado' => Solicitud::ESTADO_PENDIENTE,
                'forma_pago' => 'cheque', // Obligatorio para este tipo
                'importe' => null, // No aplica para aguinaldo por cheque
            ]);
            
            $mesNombre = $hoy->locale('es')->translatedFormat('F');

            // Mensaje de éxito
            session()->flash('success', '¡Solicitud de aguinaldo por cheque enviada correctamente!');

            // Redirigir a la vista de solicitudes
            return redirect()->route('solicitudes');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
            $this->dispatch('scroll-to-error');
        }
    }

    public function render()
    {
        return view('livewire.solicitar-aguinaldo-cheque')
            ->layout('components.layouts.autogestion');
    }
}