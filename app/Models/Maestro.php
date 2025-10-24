<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    protected $connection = 'mysql1';
    protected $table = 'munimer_inasi.in_maestro';
    protected $primaryKey = 'LEGAJO';
    public $timestamps = false;

    protected $fillable = [
        'LEGAJO',
        'TARJETA'
    ];

    /**
     * Relación con compensatorios
     */
    public function compensatorios()
    {
        return $this->hasMany(Compensatorio::class, 'CODTAR', 'TARJETA');
    }

    /**
     * Relación con movimientos
     */
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'LEGAJO', 'LEGAJO');
    }
}