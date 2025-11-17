<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TipoMovimiento extends Model
{
    protected $connection = 'mysql';
    protected $table = 'in_tipo_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'tipo_movimiento',
        'fecha_desde',
        'fecha_hasta',
        'fecha_acreditacion',
        'forma_pago',
        'importe_maximo',
    ];

    protected $casts = [
        'id' => 'integer',
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'fecha_acreditacion' => 'date',
        'importe_maximo' => 'decimal:2',
    ];

    /**
     * Constantes para los tipos de movimiento
     */
    const ADELANTO_SUELDO = 5;
    const SUELDO_CHEQUE = 6;
    const ADELANTO_CHEQUE = 7;

    /**
     * Constantes para forma de pago
     */
    const FORMA_DEPOSITO = 'deposito';
    const FORMA_CHEQUE = 'cheque';
    const FORMA_AMBOS = 'ambos';

    /**
     * Relación con solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo_movimiento');
    }

    /**
     * Verificar si es un tipo de movimiento que requiere cheque obligatorio
     */
    public function requiereChequeSi()
    {
        return $this->forma_pago === self::FORMA_CHEQUE;
    }

    /**
     * Verificar si permite elegir forma de pago
     */
    public function permiteElegirFormaPago()
    {
        return $this->forma_pago === self::FORMA_AMBOS;
    }

    /**
     * Verificar si es adelanto de sueldo (permite elegir forma de pago)
     */
    public function esAdelantoSueldo()
    {
        return $this->id === self::ADELANTO_SUELDO;
    }

    /**
     * Verificar si requiere importe
     */
    public function requiereImporte()
    {
        return $this->importe_maximo !== null;
    }

    /**
     * Verificar si está dentro del período habilitado
     */
    public function estaEnPeriodoHabilitado()
    {
        if (!$this->fecha_desde || !$this->fecha_hasta) {
            return false;
        }

        $hoy = Carbon::now();
        $fechaDesde = Carbon::parse($this->fecha_desde);
        $fechaHasta = Carbon::parse($this->fecha_hasta);

        return $hoy->between($fechaDesde, $fechaHasta);
    }

    /**
     * Obtener fecha desde formateada
     */
    public function getFechaDesdeFormateada()
    {
        return $this->fecha_desde ? Carbon::parse($this->fecha_desde)->format('d/m/Y') : '-';
    }

    /**
     * Obtener fecha hasta formateada
     */
    public function getFechaHastaFormateada()
    {
        return $this->fecha_hasta ? Carbon::parse($this->fecha_hasta)->format('d/m/Y') : '-';
    }

    /**
     * Obtener fecha acreditación formateada
     */
    public function getFechaAcreditacionFormateada()
    {
        return $this->fecha_acreditacion ? Carbon::parse($this->fecha_acreditacion)->format('d/m/Y') : '-';
    }

    /**
     * Verificar si el importe es válido
     */
    public function importeEsValido($importe)
    {
        if (!$this->requiereImporte()) {
            return true;
        }

        return $importe <= $this->importe_maximo;
    }

    /**
     * Scope para obtener tipos activos (con fechas configuradas)
     */
    public function scopeActivos($query)
    {
        return $query->whereNotNull('fecha_desde')
                     ->whereNotNull('fecha_hasta');
    }

    /**
     * Scope para obtener tipos disponibles ahora
     */
    public function scopeDisponibles($query)
    {
        $hoy = Carbon::now();
        
        return $query->whereNotNull('fecha_desde')
                     ->whereNotNull('fecha_hasta')
                     ->whereDate('fecha_desde', '<=', $hoy)
                     ->whereDate('fecha_hasta', '>=', $hoy);
    }
}