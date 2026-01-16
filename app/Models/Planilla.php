<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Planilla extends Model
{
    protected $connection = 'mysql';
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
        'confirmada' => 'integer',
        'anio' => 'integer',
        'planilla' => 'integer',
        'legajo' => 'integer',
        'dni' => 'integer',
    ];

    public function scopePorLegajo($query, $legajo)
    {
        return $query->where('legajo', $legajo);
    }

    public function scopePorPeriodo($query, $anio, $planilla)
    {
        return $query->where('anio', $anio)
                    ->where('planilla', $planilla);
    }

    public function scopePorDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('confirmada', true);
    }

    public function scopePendientes($query)
    {
        return $query->where('confirmada', false);
    }

    public function familiar()
    {
        return $this->belongsTo(Familia::class, 'dni', 'DNI')
                    ->where('LEGAJO', $this->legajo);
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'legajo', 'LEGAJO');
    }

    public function estaConfirmada()
    {
        return $this->confirmada === true;
    }

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
        $dniPadded = str_pad($this->dni, 8, '0', STR_PAD_LEFT);
        
        // Intentar primero con JPG, luego con PDF
        $nombreJpg = $dniPadded . $this->planilla . '-' . $this->anio . '.jpg';
        $nombrePdf = $dniPadded . $this->planilla . '-' . $this->anio . '.pdf';
        
        if (Storage::disk('planillas')->exists($nombreJpg)) {
            return $nombreJpg;
        }
        
        if (Storage::disk('planillas')->exists($nombrePdf)) {
            return $nombrePdf;
        }
        
        // Retornar JPG por defecto
        return $nombreJpg;
    }

    /**
     * Verificar si el archivo físico existe
     */
    public function archivoExiste()
    {
        return Storage::disk('planillas')->exists($this->getNombreArchivo());
    }

    /**
     * Obtener la URL pública del archivo
     */
    public function getUrlPublica()
    {
        if ($this->archivoExiste()) {
            return Storage::disk('planillas')->url($this->getNombreArchivo());
        }
        return null;
    }

    /**
     * Obtener la extensión del archivo
     */
    public function getExtension()
    {
        $nombre = $this->getNombreArchivo();
        return pathinfo($nombre, PATHINFO_EXTENSION);
    }
}