<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $connection = 'mysql';
    protected $table = 'munimer_inasi.in_movimie';
    public $timestamps = false;

    protected $fillable = [
        'LEGAJO',
        'CODIGO',
        'FECINASI'
    ];

    protected $casts = [
        'FECINASI' => 'date'
    ];

    /**
     * Relación con maestro
     */
    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'LEGAJO', 'LEGAJO');
    }

    /**
     * Scope para compensatorios tomados
     */
    public function scopeCompensatoriosTomados($query, $legajo)
    {
        return $query->where('legajo', $legajo)
                    ->where('codigo', 10)
                    ->where('fecinasi', '>', '2016-11-30')
                    ->orderBy('fecinasi');
    }

    /**
     * Accessor para observaciones
     */
    public function getObservacionesAttribute()
    {
        return $this->CODIGO == 10 ? 'Compensatorio tomado' : null;
    }
}