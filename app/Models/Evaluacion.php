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
}
