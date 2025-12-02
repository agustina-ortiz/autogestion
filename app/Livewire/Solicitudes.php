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
    
    // Propiedades para aguinaldo
    public $tipoAguinaldo;
    public $fechaTopeAguinaldo;
    public $mostrarAguinaldo = false;
    
    // Propiedades generales
    public $mesActual;
    public $anioActual;
    public $solicitudes = [];

    // Propiedades para el modal de edición
    public $mostrarModalEdicion = false;
    public $solicitudEditando = null;
    public $montoEdicion = null;

    // Propiedad para control de botones
    public $tieneSolicitudAdelantoPendiente = false;
    public $tieneSolicitudChequePendiente = false;
    public $tieneSolicitudAguinaldoPendiente = false;
    public $periodoAdelantosHabilitado = false;
    public $periodoChequesHabilitado = false;
    public $periodoAguinaldoHabilitado = false;

    public function mount()
    {
        // Inicializar fechas y mes
        $this->inicializarDatos();
        
        // Cargar las solicitudes del usuario
        $this->cargarSolicitudes();
        
        // Verificar si tiene solicitudes pendientes
        $this->verificarSolicitudesPendientes();

        // Verificar si los períodos están habilitados
        $this->verificarPeriodosHabilitados();
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

        // Verificar si es junio o diciembre para mostrar aguinaldo
        $this->mostrarAguinaldo = in_array($hoy->month, [6, 12]);

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

        // Obtener tipo aguinaldo por cheque (ID 7) solo si es junio o diciembre
        if ($this->mostrarAguinaldo) {
            $this->tipoAguinaldo = TipoMovimiento::find(TipoMovimiento::AGUINALDO_CHEQUE);
            
            if ($this->tipoAguinaldo) {
                $this->fechaTopeAguinaldo = $this->tipoAguinaldo->getFechaHastaFormateada();
            } else {
                // Valor por defecto si no existe el tipo
                $this->fechaTopeAguinaldo = $hoy->copy()->startOfMonth()->addDays(26)->format('d/m/Y');
            }
        }
    }

    /**
     * Verifica si el usuario tiene solicitudes pendientes de adelanto, cheque o aguinaldo este mes
     */
    private function verificarSolicitudesPendientes()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            // Verificar adelanto pendiente este mes
            $adelantoPendiente = Solicitud::porLegajo($legajoUsuario)
                ->porTipoMovimiento(TipoMovimiento::ADELANTO_SUELDO)
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            $this->tieneSolicitudAdelantoPendiente = $adelantoPendiente !== null;
            
            // Verificar cheque pendiente este mes
            $chequePendiente = Solicitud::porLegajo($legajoUsuario)
                ->porTipoMovimiento(TipoMovimiento::SUELDO_CHEQUE)
                ->whereYear('fecha_solicitud', $hoy->year)
                ->whereMonth('fecha_solicitud', $hoy->month)
                ->first();
            
            $this->tieneSolicitudChequePendiente = $chequePendiente !== null;
            
            // Verificar aguinaldo pendiente este mes (solo si se muestra)
            if ($this->mostrarAguinaldo) {
                $aguinaldoPendiente = Solicitud::porLegajo($legajoUsuario)
                    ->porTipoMovimiento(TipoMovimiento::AGUINALDO_CHEQUE)
                    ->whereYear('fecha_solicitud', $hoy->year)
                    ->whereMonth('fecha_solicitud', $hoy->month)
                    ->first();
                
                $this->tieneSolicitudAguinaldoPendiente = $aguinaldoPendiente !== null;
            }
            
        } catch (\Exception $e) {
            $this->tieneSolicitudAdelantoPendiente = false;
            $this->tieneSolicitudChequePendiente = false;
            $this->tieneSolicitudAguinaldoPendiente = false;
        }
    }

    /**
     * Verifica si los períodos de adelantos, cheques y aguinaldo están habilitados
     */
    private function verificarPeriodosHabilitados()
    {
        // Verificar período de adelantos
        if ($this->tipoAdelanto) {
            $this->periodoAdelantosHabilitado = $this->tipoAdelanto->estaEnPeriodoHabilitado();
        } else {
            $this->periodoAdelantosHabilitado = false;
        }
        
        // Verificar período de cheques
        if ($this->tipoCheque) {
            $this->periodoChequesHabilitado = $this->tipoCheque->estaEnPeriodoHabilitado();
        } else {
            $this->periodoChequesHabilitado = false;
        }
        
        // Verificar período de aguinaldo (solo si se muestra)
        if ($this->mostrarAguinaldo && $this->tipoAguinaldo) {
            $this->periodoAguinaldoHabilitado = $this->tipoAguinaldo->estaEnPeriodoHabilitado();
        } else {
            $this->periodoAguinaldoHabilitado = false;
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
                        'tipo_movimiento_id' => $solicitud->tipo_movimiento,
                        'fecha_solicitud' => $solicitud->fecha_solicitud->format('Y-m-d H:i:s'),
                        'estado' => $solicitud->getNombreEstado(),
                        'estado_raw' => $solicitud->estado,
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

    /**
     * Abre el modal para editar el monto de un adelanto
     */
    public function editarMonto($solicitudId)
    {
        try {
            $solicitud = Solicitud::find($solicitudId);
            
            // Verificar que la solicitud existe y pertenece al usuario
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            // Verificar que sea un adelanto
            if ($solicitud->tipo_movimiento !== TipoMovimiento::ADELANTO_SUELDO) {
                session()->flash('error', 'Solo se pueden editar solicitudes de adelanto.');
                return;
            }

            // Verificar que esté pendiente
            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                session()->flash('error', 'Solo se pueden editar solicitudes pendientes.');
                return;
            }

            $this->solicitudEditando = $solicitudId;
            $this->montoEdicion = $solicitud->importe;
            $this->mostrarModalEdicion = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Guarda el monto editado
     */
    public function guardarEdicion()
    {
        try {
            // Validar el monto
            if (empty($this->montoEdicion) || $this->montoEdicion <= 0) {
                session()->flash('error', 'Debe ingresar un monto válido mayor a $0.');
                return;
            }

            // Validar que no supere el máximo
            if ($this->montoEdicion > $this->montoMaximoAdelanto) {
                session()->flash('error', 'El monto no puede superar $' . number_format($this->montoMaximoAdelanto, 2, ',', '.'));
                return;
            }

            $solicitud = Solicitud::find($this->solicitudEditando);
            
            // Verificar que la solicitud existe y pertenece al usuario
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            // Verificar que esté pendiente
            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                session()->flash('error', 'Solo se pueden editar solicitudes pendientes.');
                return;
            }

            // Actualizar el monto
            $solicitud->update([
                'importe' => $this->montoEdicion
            ]);

            session()->flash('success', 'Monto actualizado correctamente a $' . number_format($this->montoEdicion, 2, ',', '.'));
            
            // Cerrar el modal y recargar solicitudes
            $this->cerrarModal();
            $this->cargarSolicitudes();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el monto: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de edición
     */
    public function cerrarModal()
    {
        $this->mostrarModalEdicion = false;
        $this->solicitudEditando = null;
        $this->montoEdicion = null;
    }

    /**
     * Elimina una solicitud
     */
    public function eliminarSolicitud($solicitudId)
    {
        try {
            $solicitud = Solicitud::find($solicitudId);
            
            // Verificar que la solicitud existe y pertenece al usuario
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            // Verificar que esté pendiente
            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                session()->flash('error', 'Solo se pueden eliminar solicitudes pendientes.');
                return;
            }

            // Guardar el tipo para el mensaje
            $tipo = $solicitud->tipoMovimiento->tipo_movimiento ?? 'solicitud';

            // Eliminar la solicitud
            $solicitud->delete();

            session()->flash('success', ucfirst($tipo) . ' eliminada correctamente.');
            
            // Recargar solicitudes y verificar pendientes
            $this->cargarSolicitudes();
            $this->verificarSolicitudesPendientes();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.solicitudes')
            ->layout('components.layouts.autogestion');
    }
}