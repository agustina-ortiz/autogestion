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
    public static function totalDiasPorTarjeta($codtar): int
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

        $legajo = DB::table('munimer_inasi.in_maestro')
            ->where('TARJETA', $codtar)
            ->value('LEGAJO');

        $tomados = $legajo
            ? Movimiento::compensatoriosTomados($legajo)->count()
            : 0;

        return round((float)(($resultado ?? 0) - $tomados), 2);
    }
}