<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'observa',
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
     * Formato: {dni}{planilla}-{año}.{ext}
     * Ejemplo: 123456781-2025.jpg
     */
    public function getNombreArchivo()
    {
        $dniPadded = str_pad($this->dni, 8, '0', STR_PAD_LEFT);
        
        // Verificar primero JPG, luego PDF
        $nombreJpg = $dniPadded . $this->planilla . '-' . $this->anio . '.jpg';
        $nombrePdf = $dniPadded . $this->planilla . '-' . $this->anio . '.pdf';
        
        $directorioPublico = public_path('fotos-licencias/fotos-empleados/planillas');
        
        if (file_exists($directorioPublico . '/' . $nombreJpg)) {
            return $nombreJpg;
        }
        
        if (file_exists($directorioPublico . '/' . $nombrePdf)) {
            return $nombrePdf;
        }
        
        // Retornar JPG por defecto (aunque no exista)
        return $nombreJpg;
    }

    /**
     * Verificar si el archivo físico existe
     */
    public function archivoExiste()
    {
        $directorioPublico = public_path('fotos-licencias/fotos-empleados/planillas');
        $nombreArchivo = $this->getNombreArchivo();
        
        return file_exists($directorioPublico . '/' . $nombreArchivo);
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

    /**
     * Obtener la extensión del archivo
     */
    public function getExtension()
    {
        $nombre = $this->getNombreArchivo();
        return pathinfo($nombre, PATHINFO_EXTENSION);
    }

    /**
     * Obtener la ruta completa del archivo en el sistema
     */
    public function getRutaCompleta()
    {
        $directorioPublico = public_path('fotos-licencias/fotos-empleados/planillas');
        return $directorioPublico . '/' . $this->getNombreArchivo();
    }
}