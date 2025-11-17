<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\TipoMovimiento;
use Carbon\Carbon;

class SolicitarAdelanto extends Component
{
    // Tipo de movimiento
    public $tipoMovimiento;
    
    // Propiedades públicas
    public $mesActual;
    public $anioActual;
    public $fechaActual;
    public $fechaInicial;
    public $fechaLimite;
    public $fechaAcreditacion;
    public $montoMaximoAdelanto;
    public $puedesolicitarAdelanto = false;
    
    // Propiedades del formulario
    public $montoSolicitado;
    public $formaCobro = '';

    public function mount()
    {
        $this->inicializarDatos();
        $this->verificarPeriodoHabilitado();
    }

    /**
     * Inicializa los datos desde in_tipo_movimiento
     */
    private function inicializarDatos()
    {
        $hoy = Carbon::now();
        
        // Mes y año actual
        $this->mesActual = $hoy->locale('es')->translatedFormat('F');
        $this->anioActual = $hoy->year;
        $this->fechaActual = $hoy->format('d/m/Y');
        
        // Obtener el tipo de movimiento adelanto de sueldo (ID 5)
        $this->tipoMovimiento = TipoMovimiento::find(TipoMovimiento::ADELANTO_SUELDO);
        
        if ($this->tipoMovimiento) {
            $this->fechaInicial = $this->tipoMovimiento->getFechaDesdeFormateada();
            $this->fechaLimite = $this->tipoMovimiento->getFechaHastaFormateada();
            $this->fechaAcreditacion = $this->tipoMovimiento->getFechaAcreditacionFormateada();
            $this->montoMaximoAdelanto = $this->tipoMovimiento->importe_maximo ?? 250000.00;
        } else {
            // Valores por defecto si no existe el tipo
            $this->fechaInicial = $hoy->copy()->startOfMonth()->format('d/m/Y');
            $this->fechaLimite = $hoy->copy()->startOfMonth()->addDays(6)->format('d/m/Y');
            $this->fechaAcreditacion = $hoy->copy()->startOfMonth()->addDays(9)->format('d/m/Y');
            $this->montoMaximoAdelanto = 250000.00;
        }
    }

    /**
     * Verifica si el usuario está en el período habilitado para solicitar adelantos
     */
    private function verificarPeriodoHabilitado()
    {
        // Verificar que exista el tipo de movimiento
        if (!$this->tipoMovimiento) {
            $this->puedesolicitarAdelanto = false;
            return;
        }

        // Verificar si está en período habilitado usando el método del modelo
        if (!$this->tipoMovimiento->estaEnPeriodoHabilitado()) {
            $this->puedesolicitarAdelanto = false;
            return;
        }

        // Verificar si ya tiene un adelanto pendiente este mes
        if ($this->tieneAdelantoPendiente()) {
            $this->puedesolicitarAdelanto = false;
            return;
        }

        // Si pasó todas las validaciones, puede solicitar
        $this->puedesolicitarAdelanto = true;
    }

    /**
     * Verifica si el usuario ya tiene un adelanto pendiente o listo este mes
     */
    private function tieneAdelantoPendiente()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            $adelanto = Solicitud::porLegajo($legajoUsuario)
                ->porTipoMovimiento(TipoMovimiento::ADELANTO_SUELDO)
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

        // Validar usando el método del modelo
        if (!$this->tipoMovimiento->importeEsValido($this->montoSolicitado)) {
            session()->flash('error', 'El monto solicitado no puede superar $' . number_format($this->montoMaximoAdelanto, 2, ',', '.'));
            return false;
        }

        return true;
    }

    /**
     * Valida la forma de cobro
     */
    private function validarFormaCobro()
    {
        if (empty($this->formaCobro)) {
            session()->flash('error', 'Debe seleccionar una forma de cobro.');
            return false;
        }

        if (!in_array($this->formaCobro, ['efectivo', 'cheque'])) {
            session()->flash('error', 'Forma de cobro inválida.');
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

        // Validar la forma de cobro
        if (!$this->validarFormaCobro()) {
            return;
        }

        // Verificar nuevamente que no tenga solicitud pendiente
        if ($this->tieneAdelantoPendiente()) {
            session()->flash('error', 'Ya tiene una solicitud de adelanto pendiente o procesada para este mes.');
            return;
        }

        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            
            // Mapear 'efectivo' a 'deposito' para que coincida con el enum de la BD
            $formaPago = $this->formaCobro === 'efectivo' ? 'deposito' : 'cheque';
            
            // Crear la solicitud usando el modelo
            Solicitud::create([
                'legajo' => $legajoUsuario,
                'fecha_solicitud' => now(),
                'tipo_movimiento' => TipoMovimiento::ADELANTO_SUELDO,
                'origen' => Solicitud::ORIGEN_AUTOGESTION,
                'estado' => Solicitud::ESTADO_PENDIENTE,
                'forma_pago' => $formaPago,
                'importe' => $this->montoSolicitado,
            ]);
            
            // Mensaje de éxito
            session()->flash('success', '¡Solicitud de adelanto enviada correctamente por $' . number_format($this->montoSolicitado, 2, ',', '.') . '! Será procesada en breve.');

            // Redirigir a la vista de solicitudes
            return redirect()->route('solicitudes');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.solicitar-adelanto')
            ->layout('components.layouts.autogestion');
    }
}