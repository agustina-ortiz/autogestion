<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    // INASI viejo, igual que Compensatorio y Movimiento: conexion por defecto
    // con la base calificada en el nombre de tabla. La conexion 'mysql1' no
    // tiene credenciales en .env, asi que no sirve fuera de desarrollo.
    protected $connection = 'mysql';
    protected $table = 'munimer_inasi.in_desempeno';
    public $timestamps = false;

    protected $fillable = ['LEGAJO', 'FECHA', 'PUNTUACION', 'OBSERVA'];

    /**
     * INASI guarda la calificacion como numero, pero al empleado se le muestra
     * la letra. A es la mejor.
     */
    public const CALIFICACIONES = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
    ];

    /**
     * Letra que le corresponde a la puntuacion.
     *
     * Si llegara un valor fuera de la escala se devuelve tal cual en vez de
     * ocultarlo, para que se note que hay algo mal cargado.
     */
    public function getCalificacionAttribute()
    {
        return self::CALIFICACIONES[$this->PUNTUACION] ?? $this->PUNTUACION;
    }

    /**
     * Clases de color del badge segun la calificacion.
     */
    public function getCalificacionColorAttribute(): string
    {
        return match ($this->PUNTUACION) {
            1 => 'bg-green-100 text-green-700',
            2 => 'bg-yellow-100 text-yellow-700',
            3 => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
