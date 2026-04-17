<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Compensatorio extends Model
{
    protected $connection = 'mysql';
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
        $resultado = DB::table('munimer_inasi.in_compensa')
            ->selectRaw("
                SUM(
                    COMP151 / 4 +
                    COMP152 / IF(FECHA < '2016-11-01', 4, 3) +
                    COMP153 / 3
                ) AS dias
            ")
            ->where('CODTAR', $codtar)
            ->value('dias');

        return (int) ($resultado ?? 0);
    }
}