<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    protected $connection = 'mysql'; // Usar la misma conexión que Familia
    protected $table = 'in_planillas';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'legajo',
        'anio',
        'planilla',
        'dni',
        'fecha',
        'confirmada',
    ];

    protected $casts = [
        'fecha' => 'date',
        'confirmada' => 'boolean',
        'anio' => 'integer',
        'planilla' => 'integer',
        'legajo' => 'integer',
        'dni' => 'integer',
    ];

    /**
     * Scope para filtrar por legajo
     */
    public function scopePorLegajo($query, $legajo)
    {
        return $query->where('legajo', $legajo);
    }

    /**
     * Scope para filtrar por año y planilla
     */
    public function scopePorPeriodo($query, $anio, $planilla)
    {
        return $query->where('anio', $anio)
                    ->where('planilla', $planilla);
    }

    /**
     * Scope para filtrar por DNI
     */
    public function scopePorDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }

    /**
     * Scope para planillas confirmadas
     */
    public function scopeConfirmadas($query)
    {
        return $query->where('confirmada', true);
    }

    /**
     * Scope para planillas pendientes de confirmación
     */
    public function scopePendientes($query)
    {
        return $query->where('confirmada', false);
    }

    /**
     * Relación con Familia (hijo)
     */
    public function familiar()
    {
        return $this->belongsTo(Familia::class, 'dni', 'DNI')
                    ->where('LEGAJO', $this->legajo);
    }

    /**
     * Relación con el empleado (User)
     */
    public function empleado()
    {
        return $this->belongsTo(User::class, 'legajo', 'LEGAJO');
    }

    /**
     * Verificar si la planilla está confirmada por RRHH
     */
    public function estaConfirmada()
    {
        return $this->confirmada === true;
    }

    /**
     * Confirmar planilla
     */
    public function confirmar()
    {
        $this->confirmada = true;
        return $this->save();
    }

    /**
     * Obtener el nombre del archivo de la planilla
     */
    public function getNombreArchivo()
    {
        return str_pad($this->dni, 8, '0', STR_PAD_LEFT) . 
               $this->planilla . '-' . 
               $this->anio . '.jpg';
    }

    /**
     * Obtener la ruta completa del archivo
     */
    public function getRutaCompleta()
    {
        return public_path('fotos-licencias/fotos-empleados/planillas/' . $this->getNombreArchivo());
    }

    /**
     * Verificar si el archivo físico existe
     */
    public function archivoExiste()
    {
        return file_exists($this->getRutaCompleta());
    }

    /**
     * Obtener la URL pública del archivo
     */
    public function getUrlPublica()
    {
        if ($this->archivoExiste()) {
            return asset('fotos-licencias/fotos-empleados/planillas/' . $this->getNombreArchivo());
        }
        return null;
    }
}