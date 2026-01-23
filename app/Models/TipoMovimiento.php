<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TipoMovimiento extends Model
{
    protected $table = 'in_tipo_movimiento';
    
    protected $fillable = [
        'tipo_movimiento',
        'periodo',
        'fecha_desde',
        'fecha_hasta',
        'fecha_acreditacion',
        'forma_pago',
        'importe_maximo'
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'fecha_acreditacion' => 'date',
        'importe_maximo' => 'decimal:2'
    ];

    // Constantes para forma_pago
    const FORMA_PAGO_DEPOSITO = 'deposito';
    const FORMA_PAGO_CHEQUE = 'cheque';
    const FORMA_PAGO_AMBOS = 'ambos';

    /**
     * Scope para obtener tipos de movimiento vigentes (dentro del rango de fechas)
     */
    public function scopeVigentes($query)
    {
        $hoy = Carbon::now()->startOfDay();
        
        return $query->where('fecha_desde', '<=', $hoy)
                     ->where('fecha_hasta', '>=', $hoy)
                     ->orderBy('id', 'asc');
    }

    /**
     * Verifica si el tipo de movimiento está vigente
     */
    public function estaVigente()
    {
        $hoy = Carbon::now()->startOfDay();
        $fechaDesde = Carbon::parse($this->fecha_desde)->startOfDay();
        $fechaHasta = Carbon::parse($this->fecha_hasta)->endOfDay();
        
        return $hoy->between($fechaDesde, $fechaHasta);
    }

    /**
     * Verifica si está en período habilitado (alias de estaVigente para mantener compatibilidad)
     */
    public function estaEnPeriodoHabilitado()
    {
        return $this->estaVigente();
    }

    /**
     * Valida si un importe es válido para este tipo de movimiento
     */
    public function importeEsValido($importe)
    {
        if (is_null($this->importe_maximo)) {
            return true;
        }
        
        return $importe > 0 && $importe <= $this->importe_maximo;
    }

    /**
     * Verifica si requiere ingresar un importe
     */
    public function requiereImporte()
    {
        return !is_null($this->importe_maximo);
    }

    /**
     * Verifica si permite seleccionar forma de pago
     */
    public function permiteSeleccionarFormaPago()
    {
        return $this->forma_pago === self::FORMA_PAGO_AMBOS;
    }

    /**
     * Verifica si solo permite depósito
     */
    public function soloDeposito()
    {
        return $this->forma_pago === self::FORMA_PAGO_DEPOSITO;
    }

    /**
     * Verifica si solo permite cheque
     */
    public function soloCheque()
    {
        return $this->forma_pago === self::FORMA_PAGO_CHEQUE;
    }

    /**
     * Obtiene la fecha desde formateada
     */
    public function getFechaDesdeFormateada()
    {
        return $this->fecha_desde ? Carbon::parse($this->fecha_desde)->format('d/m/Y') : null;
    }

    /**
     * Obtiene la fecha hasta formateada
     */
    public function getFechaHastaFormateada()
    {
        return $this->fecha_hasta ? Carbon::parse($this->fecha_hasta)->format('d/m/Y') : null;
    }

    /**
     * Obtiene la fecha de acreditación formateada
     */
    public function getFechaAcreditacionFormateada()
    {
        return $this->fecha_acreditacion ? Carbon::parse($this->fecha_acreditacion)->format('d/m/Y') : null;
    }

    /**
     * Obtiene el periodo formateado (ejemplo: "ENERO 2026" o valor personalizado)
     */
    public function getPeriodoFormateado()
    {
        // Si tiene periodo personalizado, usarlo
        if (!empty($this->periodo)) {
            return $this->periodo;
        }
        
        // Si no, generar a partir de la fecha_desde
        if ($this->fecha_desde) {
            $fecha = Carbon::parse($this->fecha_desde);
            $mes = strtoupper($fecha->locale('es')->translatedFormat('F'));
            $anio = $fecha->year;
            return "{$mes} {$anio}";
        }
        
        return null;
    }

    /**
     * Obtiene el color de la sección basado en el ID del tipo
     * Los colores se rotan entre los tipos disponibles
     */
    public function getColorSeccion()
    {
        $colores = [
            ['bg' => 'yellow-50', 'border' => 'yellow-400', 'text' => 'yellow-700', 'badge' => 'yellow-100', 'badge-text' => 'yellow-800'],
            ['bg' => 'blue-50', 'border' => 'blue-400', 'text' => 'blue-700', 'badge' => 'blue-100', 'badge-text' => 'blue-800'],
            ['bg' => 'purple-50', 'border' => 'purple-400', 'text' => 'purple-700', 'badge' => 'purple-100', 'badge-text' => 'purple-800'],
            ['bg' => 'green-50', 'border' => 'green-400', 'text' => 'green-700', 'badge' => 'green-100', 'badge-text' => 'green-800'],
            ['bg' => 'red-50', 'border' => 'red-400', 'text' => 'red-700', 'badge' => 'red-100', 'badge-text' => 'red-800'],
            ['bg' => 'orange-50', 'border' => 'orange-400', 'text' => 'orange-700', 'badge' => 'orange-100', 'badge-text' => 'orange-800'],
            ['bg' => 'pink-50', 'border' => 'pink-400', 'text' => 'pink-700', 'badge' => 'pink-100', 'badge-text' => 'pink-800'],
            ['bg' => 'indigo-50', 'border' => 'indigo-400', 'text' => 'indigo-700', 'badge' => 'indigo-100', 'badge-text' => 'indigo-800'],
        ];

        // Rotar colores basándose en el ID del tipo
        $indice = ($this->id - 1) % count($colores);
        return $colores[$indice];
    }

    /**
     * Obtiene el texto de la forma de pago formateado
     */
    public function getFormaPagoTexto()
    {
        return match($this->forma_pago) {
            self::FORMA_PAGO_DEPOSITO => 'DEPÓSITO',
            self::FORMA_PAGO_CHEQUE => 'CHEQUE',
            self::FORMA_PAGO_AMBOS => 'DEPÓSITO o CHEQUE',
            default => 'No especificado'
        };
    }

    /**
     * Obtiene una descripción dinámica para la sección
     */
    public function getDescripcionSeccion()
    {
        $descripcion = [];
        
        // Texto sobre el periodo
        $periodo = $this->getPeriodoFormateado();
        if ($periodo) {
            if ($this->requiereImporte()) {
                $descripcion[] = "Los " . strtolower($this->tipo_movimiento) . " correspondientes a {$periodo}";
            } else {
                $descripcion[] = "La solicitud correspondiente a {$periodo}";
            }
        }
        
        // Texto sobre el rango de fechas
        if ($this->fecha_desde && $this->fecha_hasta) {
            $desde = $this->getFechaDesdeFormateada();
            $hasta = $this->getFechaHastaFormateada();
            
            if ($this->requiereImporte()) {
                $descripcion[] = "deberá solicitarse entre el día {$desde} y el {$hasta}";
            } else {
                $descripcion[] = "podrá solicitarse hasta el día {$hasta}";
            }
        }
        
        // Texto sobre el monto máximo
        if ($this->importe_maximo) {
            $monto = number_format($this->importe_maximo, 0, ',', '.');
            $descripcion[] = "y no podrá superar el valor de \${$monto}";
        }
        
        return implode(' ', $descripcion);
    }

    /**
     * Obtiene el texto de acreditación
     */
    public function getTextoAcreditacion()
    {
        if ($this->fecha_acreditacion) {
            $fecha = $this->getFechaAcreditacionFormateada();
            
            if ($this->requiereImporte()) {
                return "Serán depositados en el transcurso del día {$fecha}.";
            } else {
                return "La forma de pago será procesada el día {$fecha}.";
            }
        }
        
        return null;
    }

    /**
     * Obtiene el texto del modal de confirmación
     */
    public function getTextoConfirmacionModal()
    {
        $periodo = $this->getPeriodoFormateado();
        $formaPago = $this->getFormaPagoTexto();
        
        if ($this->requiereImporte()) {
            return "El importe solicitado está sujeto a disponibilidad y será evaluado por RRHH.";
        } else {
            return "Confirme que desea recibir su {$this->tipo_movimiento} de {$periodo} por {$formaPago}.";
        }
    }

    /**
     * Relación con solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tipo_movimiento');
    }
}