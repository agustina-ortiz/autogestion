<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Compensatorio extends Model
{
    protected $connection = 'mysql1';
    protected $table = 'munimer_inasi.in_compensa';
    protected $primaryKey = 'CODTAR';
    public $timestamps = false;

    protected $fillable = [
        'CODTAR',
        'COMP151',
        'COMP152',
        'COMP153',
        'FECHA'
    ];

    /**
     * Relación con la tarjeta del maestro
     */
    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'CODTAR', 'TARJETA');
    }

    /**
     * Scope para obtener total de días por tarjeta
     * Solo toma el registro más reciente (por fecha)
     */
    public function scopeTotalDiasPorTarjeta($query, $codtar)
    {
        $registro = $query->where('CODTAR', $codtar)
                         ->orderBy('FECHA', 'DESC')
                         ->first();
        
        if (!$registro) {
            return 0;
        }

        $divisor152 = $registro->FECHA < '2016-11-01' ? 4 : 3;
        
        return ($registro->COMP151 / 4) + 
               ($registro->COMP152 / $divisor152) + 
               ($registro->COMP153 / 3);
    }
}