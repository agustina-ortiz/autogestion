<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $connection = 'mysql';
    protected $table = 'in_solicitudes';
    public $timestamps = false;

    protected $fillable = [
        'legajo',
        'fecha_solicitud',
        'tipo_movimiento',
        'origen',
        'estado',
        'forma_pago',
        'importe',
    ];

    protected $casts = [
        'legajo' => 'integer',
        'fecha_solicitud' => 'date',
        'tipo_movimiento' => 'integer',
        'origen' => 'integer',
        'estado' => 'integer',
        'importe' => 'decimal:2',
    ];

    /**
     * Constantes para origen
     */
    const ORIGEN_AUTOGESTION = 1;

    /**
     * Constantes para estado
     */
    const ESTADO_PENDIENTE = 1;
    const ESTADO_LISTO = 2;

    /**
     * Constantes para forma de pago
     */
    const FORMA_PAGO_DEPOSITO = 'deposito';
    const FORMA_PAGO_CHEQUE = 'cheque';

    /**
     * Relación con el empleado (in_maestro)
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'legajo', 'LEGAJO');
    }

    /**
     * Relación con el tipo de movimiento
     */
    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class, 'tipo_movimiento');
    }

    /**
     * Scope para filtrar por estado pendiente
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    /**
     * Scope para filtrar por estado listo
     */
    public function scopeListas($query)
    {
        return $query->where('estado', self::ESTADO_LISTO);
    }

    /**
     * Scope para filtrar por legajo
     */
    public function scopePorLegajo($query, $legajo)
    {
        return $query->where('legajo', $legajo);
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopePorTipoMovimiento($query, $tipoMovimiento)
    {
        return $query->where('tipo_movimiento', $tipoMovimiento);
    }

    /**
     * Scope para ordenar por fecha más reciente
     */
    public function scopeMasRecientes($query)
    {
        return $query->orderBy('fecha_solicitud', 'desc')
                     ->orderBy('id', 'desc');
    }

    /**
     * Verificar si la solicitud está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Verificar si la solicitud está lista
     */
    public function estaLista()
    {
        return $this->estado === self::ESTADO_LISTO;
    }

    /**
     * Marcar solicitud como lista
     */
    public function marcarComoLista()
    {
        $this->estado = self::ESTADO_LISTO;
        return $this->save();
    }

    /**
     * Verificar si el tipo de movimiento requiere cheque obligatorio
     */
    public function requiereChequeSi()
    {
        return in_array($this->tipo_movimiento, [
            TipoMovimiento::SUELDO_CHEQUE,
            TipoMovimiento::ADELANTO_CHEQUE
        ]);
    }

    /**
     * Verificar si es adelanto de sueldo
     */
    public function esAdelantoSueldo()
    {
        return $this->tipo_movimiento === TipoMovimiento::ADELANTO_SUELDO;
    }

    /**
     * Validar que el importe no exceda el máximo permitido
     */
    public function importeEsValido()
    {
        if (!$this->esAdelantoSueldo()) {
            return true;
        }

        $tipoMovimiento = $this->tipoMovimiento; // ← Usa in_tipo_movimiento
        
        if (!$tipoMovimiento || !$tipoMovimiento->importe_maximo) {
            return false;
        }

        return $this->importe <= $tipoMovimiento->importe_maximo;
    }

    /**
     * Obtener el nombre del estado
     */
    public function getNombreEstado()
    {
        return match($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_LISTO => 'Listo',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener el nombre del origen
     */
    public function getNombreOrigen()
    {
        return match($this->origen) {
            self::ORIGEN_AUTOGESTION => 'Autogestión',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener el importe máximo permitido del empleado
     */
    public function getImporteMaximo()
    {
        $tipoMovimiento = $this->tipoMovimiento; // ← Usa in_tipo_movimiento
        return $tipoMovimiento?->importe_maximo ?? 0;
    }
}