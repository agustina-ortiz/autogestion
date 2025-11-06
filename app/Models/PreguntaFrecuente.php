<?php
// app/Models/PreguntaFrecuente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaFrecuente extends Model
{
    protected $connection = 'mysql';
    protected $table = 'in_preguntas_frecuentes';
    
    protected $fillable = [
        'pregunta',
        'respuesta'
    ];

    public function scopeBuscar($query, $termino)
    {
        if (empty($termino)) {
            return $query;
        }

        return $query->where(function($q) use ($termino) {
            $q->where('pregunta', 'like', '%' . $termino . '%')
              ->orWhere('respuesta', 'like', '%' . $termino . '%');
        });
    }
}