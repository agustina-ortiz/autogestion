<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\TipoMovimiento;
use Carbon\Carbon;

class Solicitudes extends Component
{
    // Tipos de movimiento vigentes (colección dinámica)
    public $tiposMovimientoVigentes = [];
    
    // Propiedades generales
    public $solicitudes = [];

    // Control de modal dinámico
    public $mostrarModal = false;
    public $tipoMovimientoSeleccionado = null;
    public $mostrarModalEdicion = false;

    // Propiedades del formulario dinámico
    public $montoSolicitado;
    public $formaCobro = '';

    // Propiedades para edición
    public $solicitudEditando = null;
    public $montoEdicion = null;

    // Control de solicitudes pendientes por tipo
    public $solicitudesPendientesPorTipo = [];

    public function mount()
    {
        $this->inicializarDatos();
        $this->cargarSolicitudes();
        $this->verificarSolicitudesPendientes();
    }

    /**
     * Inicializa los datos y carga tipos de movimiento vigentes
     */
    private function inicializarDatos()
    {
        // Cargar todos los tipos de movimiento vigentes de forma dinámica
        $this->tiposMovimientoVigentes = TipoMovimiento::vigentes()->get();
    }

    /**
     * Verifica si el usuario tiene solicitudes pendientes para cada tipo
     */
    private function verificarSolicitudesPendientes()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $hoy = Carbon::now();
            
            $this->solicitudesPendientesPorTipo = [];
            
            foreach ($this->tiposMovimientoVigentes as $tipo) {
                $solicitudPendiente = Solicitud::porLegajo($legajoUsuario)
                    ->porTipoMovimiento($tipo->id)
                    ->whereYear('fecha_solicitud', $hoy->year)
                    ->whereMonth('fecha_solicitud', $hoy->month)
                    ->first();
                
                $this->solicitudesPendientesPorTipo[$tipo->id] = $solicitudPendiente !== null;
            }
            
        } catch (\Exception $e) {
            $this->solicitudesPendientesPorTipo = [];
        }
    }

    /**
     * Verifica si un tipo tiene solicitud pendiente
     */
    public function tieneSolicitudPendiente($tipoId)
    {
        return $this->solicitudesPendientesPorTipo[$tipoId] ?? false;
    }

    /**
     * Carga las solicitudes del usuario
     */
    private function cargarSolicitudes()
    {
        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            
            $this->solicitudes = Solicitud::porLegajo($legajoUsuario)
                ->with('tipoMovimiento')
                ->masRecientes()
                ->get()
                ->map(function ($solicitud) {
                    $color = $solicitud->tipoMovimiento ? $solicitud->tipoMovimiento->getColorSeccion() : ['badge' => 'gray-100', 'badge-text' => 'gray-800'];
                    
                    return (object)[
                        'id' => $solicitud->id,
                        'tipo' => $solicitud->tipoMovimiento->tipo_movimiento ?? 'Desconocido',
                        'tipo_movimiento_id' => $solicitud->tipo_movimiento,
                        'fecha_solicitud' => $solicitud->fecha_solicitud->format('Y-m-d H:i:s'),
                        'estado' => $solicitud->getNombreEstado(),
                        'estado_raw' => $solicitud->estado,
                        'monto' => $solicitud->importe,
                        'requiere_importe' => $solicitud->tipoMovimiento ? $solicitud->tipoMovimiento->requiereImporte() : false,
                        'observaciones' => $solicitud->forma_pago === 'cheque' 
                            ? 'Forma de pago: Cheque' 
                            : 'Forma de pago: Depósito',
                        'color_badge' => $color['badge'],
                        'color_badge_text' => $color['badge-text'],
                    ];
                })
                ->toArray();
                
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar las solicitudes: ' . $e->getMessage());
            $this->solicitudes = [];
        }
    }

    // =========================
    // GESTIÓN DE MODAL DINÁMICO
    // =========================

    /**
     * Abre el modal para un tipo de movimiento específico
     */
    public function abrirModal($tipoMovimientoId)
    {
        $tipo = TipoMovimiento::find($tipoMovimientoId);
        
        if (!$tipo || !$tipo->estaVigente() || $this->tieneSolicitudPendiente($tipoMovimientoId)) {
            return;
        }

        $this->tipoMovimientoSeleccionado = $tipo;
        $this->montoSolicitado = '';
        $this->formaCobro = '';
        $this->mostrarModal = true;
    }

    /**
     * Confirma la solicitud del tipo de movimiento seleccionado
     */
    public function confirmarSolicitud()
    {
        if (!$this->tipoMovimientoSeleccionado) {
            session()->flash('error', 'No se ha seleccionado un tipo de movimiento.');
            return;
        }

        // Validar monto si es requerido
        if ($this->tipoMovimientoSeleccionado->requiereImporte()) {
            if (empty($this->montoSolicitado) || $this->montoSolicitado <= 0) {
                session()->flash('error', 'Debe ingresar un monto válido mayor a $0.');
                return;
            }

            if (!$this->tipoMovimientoSeleccionado->importeEsValido($this->montoSolicitado)) {
                session()->flash('error', 'El monto no puede superar $' . number_format($this->tipoMovimientoSeleccionado->importe_maximo, 0, ',', '.'));
                return;
            }
        }

        // Validar forma de cobro si es necesario
        if ($this->tipoMovimientoSeleccionado->permiteSeleccionarFormaPago()) {
            if (empty($this->formaCobro)) {
                session()->flash('error', 'Debe seleccionar una forma de cobro.');
                return;
            }
        } else {
            // Si no permite seleccionar, usar la forma de pago del tipo de movimiento
            $this->formaCobro = $this->tipoMovimientoSeleccionado->forma_pago;
        }

        try {
            $legajoUsuario = Auth::user()->LEGAJO;
            $formaPago = $this->formaCobro;
            
            // Crear la solicitud
            Solicitud::create([
                'legajo' => $legajoUsuario,
                'fecha_solicitud' => now(),
                'tipo_movimiento' => $this->tipoMovimientoSeleccionado->id,
                'origen' => Solicitud::ORIGEN_AUTOGESTION,
                'estado' => Solicitud::ESTADO_PENDIENTE,
                'forma_pago' => $formaPago,
                'importe' => $this->tipoMovimientoSeleccionado->requiereImporte() ? $this->montoSolicitado : null,
            ]);
            
            // Mensaje de éxito
            $mensaje = '¡Solicitud de ' . strtolower($this->tipoMovimientoSeleccionado->tipo_movimiento) . ' enviada correctamente';
            if ($this->tipoMovimientoSeleccionado->requiereImporte()) {
                $mensaje .= ' por $' . number_format($this->montoSolicitado, 0, ',', '.');
            }
            $mensaje .= '!';
            
            session()->flash('success', $mensaje);
            
            $this->cerrarModal();
            $this->cargarSolicitudes();
            $this->verificarSolicitudesPendientes();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal dinámico
     */
    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->tipoMovimientoSeleccionado = null;
        $this->montoSolicitado = '';
        $this->formaCobro = '';
    }

    // =========================
    // EDICIÓN Y ELIMINACIÓN
    // =========================

    /**
     * Edita el monto de una solicitud (solo para tipos que requieren importe)
     */
    public function editarMonto($solicitudId)
    {
        try {
            $solicitud = Solicitud::find($solicitudId);
            
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            // Verificar que el tipo de movimiento requiera importe
            $tipoMovimiento = $solicitud->tipoMovimiento;
            if (!$tipoMovimiento || !$tipoMovimiento->requiereImporte()) {
                session()->flash('error', 'Solo se pueden editar solicitudes que requieren monto.');
                return;
            }

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
     * Guarda la edición del monto
     */
    public function guardarEdicion()
    {
        try {
            if (empty($this->montoEdicion) || $this->montoEdicion <= 0) {
                session()->flash('error', 'Debe ingresar un monto válido mayor a $0.');
                return;
            }

            $solicitud = Solicitud::find($this->solicitudEditando);
            
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            $tipoMovimiento = $solicitud->tipoMovimiento;
            if ($tipoMovimiento && !$tipoMovimiento->importeEsValido($this->montoEdicion)) {
                session()->flash('error', 'El monto no puede superar $' . number_format($tipoMovimiento->importe_maximo, 0, ',', '.'));
                return;
            }

            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                session()->flash('error', 'Solo se pueden editar solicitudes pendientes.');
                return;
            }

            $solicitud->update(['importe' => $this->montoEdicion]);

            session()->flash('success', 'Monto actualizado correctamente a $' . number_format($this->montoEdicion, 0, ',', '.'));
            
            $this->cerrarModalEdicion();
            $this->cargarSolicitudes();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el monto: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de edición
     */
    public function cerrarModalEdicion()
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
            
            if (!$solicitud || $solicitud->legajo !== Auth::user()->LEGAJO) {
                session()->flash('error', 'Solicitud no encontrada.');
                return;
            }

            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                session()->flash('error', 'Solo se pueden eliminar solicitudes pendientes.');
                return;
            }

            $tipo = $solicitud->tipoMovimiento->tipo_movimiento ?? 'solicitud';
            $solicitud->delete();

            session()->flash('success', ucfirst($tipo) . ' eliminada correctamente.');
            
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