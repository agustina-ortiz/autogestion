<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $connection = 'mysql1';
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
        return $query->where('LEGAJO', $legajo)
                     ->where('CODIGO', 10)
                     ->where('FECINASI', '>', '2016-11-30')
                     ->orderBy('FECINASI');
    }

    /**
     * Accessor para observaciones
     */
    public function getObservacionesAttribute()
    {
        return $this->CODIGO == 10 ? 'Compensatorio tomado' : null;
    }
}