<?php
// app/Models/Familia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    protected $connection = 'mysql'; // Especificar la conexión
    protected $table = 'in_familia';
    
    // Indicar que no hay primary key simple
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'LEGAJO',
        'NOMBRE',
        'DNI',
        'FECHA_NAC',
        'TIPOFAMI',
    ];

    // Relación con el maestro (empleado)
    public function empleado()
    {
        return $this->belongsTo(User::class, 'LEGAJO', 'LEGAJO');
    }

    
    // Scope para filtrar hijos
    public function scopeHijos($query)
    {
        return $query->where('TIPOFAMI', '>', 1);
    }

    /**
     * Contar hijos de un empleado
     * 
     * @param int $legajo
     * @return int
     */
    public static function contarHijos($legajo)
    {
        return self::join('in_maestro as m', 'm.legajo', '=', 'in_familia.LEGAJO')
            ->where('in_familia.LEGAJO', $legajo)
            ->where('in_familia.TIPOFAMI', '>', 1)
            ->where('m.sexo', 1)
            ->count();
    }

    /**
     * Obtener todos los hijos de un empleado
     * 
     * @param int $legajo
     * @return \Illuminate\Support\Collection
     */
    public static function obtenerHijos($legajo)
    {
        return self::select(
                'in_familia.NOMBRE as nombre',
                'in_familia.DNI as dni',
                'in_familia.FECHA_NAC as fecha_nac',
                'm.sexo as sexo'
            )
            ->leftJoin('in_maestro as m', 'm.legajo', '=', 'in_familia.LEGAJO')
            ->where('in_familia.LEGAJO', $legajo)
            ->where('in_familia.TIPOFAMI', '>', 1)
            ->where('m.sexo', 1)
            ->get();
    }

    /**
     * Verificar si un empleado tiene hijos
     * 
     * @param int $legajo
     * @return bool
     */
    public static function tieneHijos($legajo)
    {
        return self::contarHijos($legajo) > 0;
    }
}