<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $connection = 'mysql1';
    protected $table = 'in_desempeno';
    public $timestamps = false;

    protected $fillable = ['LEGAJO', 'FECHA', 'PUNTUACION', 'OBSERVA'];
}
