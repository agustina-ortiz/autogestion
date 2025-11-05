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
        'TIPOESCO',
        'CURSO',
        'ESCUELA',
        'PLANILLA1',
        'PLANILLA2',
    ];

    protected $casts = [
        'FECHA_NAC' => 'date',
        'TIPOFAMI' => 'integer',
        'TIPOESCO' => 'integer',
        'LEGAJO' => 'integer',
        'DNI' => 'integer',
    ];

    // Relación con el maestro (empleado)
    public function empleado()
    {
        return $this->belongsTo(User::class, 'LEGAJO', 'LEGAJO');
    }

    // Relación con planillas
    public function planillas()
    {
        return $this->hasMany(Planilla::class, 'dni', 'DNI')
            ->where('legajo', $this->LEGAJO);
    }

    /**
     * Scope para filtrar todos los miembros de familia (TIPOFAMI 1, 2, 3, 4)
     * Anteriormente filtraba solo > 1, lo que excluía tipos 3 y 4
     */
    public function scopeHijos($query)
    {
        return $query->whereIn('TIPOFAMI', [1, 2, 3, 4]);
    }

    /**
     * Scope para filtrar miembros de familia elegibles para planillas
     * TIPOFAMI > 1 (excluye cónyuge) y TIPOESCO en P, S, J, L
     * 
     * TIPOESCO:
     * P = Primaria
     * S = Secundaria
     * J = Jardin/Inicial
     * L = Universitaria/Terciaria
     */
    public function scopeSoloHijos($query)
    {
        return $query->where('TIPOFAMI', '>', 1)
                    ->whereIn('TIPOESCO', ['P', 'S', 'J', 'L']);
    }
    
    /**
     * Scope para filtrar solo por tipo de escolaridad válido
     */
    public function scopeConEscolaridad($query)
    {
        return $query->whereIn('TIPOESCO', ['P', 'S', 'J', 'L']);
    }
    
    /**
     * Scope alternativo para filtrar SOLO hijos biológicos/adoptados (TIPOFAMI = 2)
     */
    public function scopeHijosBiologicos($query)
    {
        return $query->where('TIPOFAMI', 2);
    }

    /**
     * Scope para filtrar por legajo
     */
    public function scopePorLegajo($query, $legajo)
    {
        return $query->where('LEGAJO', $legajo);
    }

    /**
     * Obtener el estado de la planilla para un período específico
     * 
     * @param int $numeroPlanilla
     * @param int $anio
     * @return array
     */
    public function getEstadoPlanilla($numeroPlanilla, $anio)
    {
        $campoPlanilla = 'PLANILLA' . $numeroPlanilla;
        $nombreArchivo = str_pad($this->DNI, 8, '0', STR_PAD_LEFT) . 
                        $numeroPlanilla . '-' . 
                        $anio . '.jpg';
        
        $rutaCompleta = public_path('fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo);
        $archivoExiste = file_exists($rutaCompleta);
        
        // Verificar si existe en in_planillas
        $existeEnPlanillas = Planilla::where('legajo', $this->LEGAJO)
            ->where('dni', $this->DNI)
            ->where('planilla', $numeroPlanilla)
            ->where('anio', $anio)
            ->exists();
        
        $campoBD = $this->$campoPlanilla;
        
        if ($archivoExiste && $existeEnPlanillas && $campoBD === 'S') {
            return [
                'estado' => 'subida',
                'tiene_planilla' => true,
            ];
        } elseif ($archivoExiste && $existeEnPlanillas) {
            return [
                'estado' => 'proceso',
                'tiene_planilla' => true,
            ];
        }
        
        return [
            'estado' => 'pendiente',
            'tiene_planilla' => false,
        ];
    }

    /**
     * Verificar si tiene planilla subida para un período
     * 
     * @param int $numeroPlanilla
     * @param int $anio
     * @return bool
     */
    public function tienePlanillaSubida($numeroPlanilla, $anio)
    {
        $estado = $this->getEstadoPlanilla($numeroPlanilla, $anio);
        return $estado['tiene_planilla'];
    }

    /**
     * Obtener la ruta del archivo de planilla
     * 
     * @param int $numeroPlanilla
     * @param int $anio
     * @return string|null
     */
    public function getRutaPlanilla($numeroPlanilla, $anio)
    {
        $nombreArchivo = str_pad($this->DNI, 8, '0', STR_PAD_LEFT) . 
                        $numeroPlanilla . '-' . 
                        $anio . '.jpg';
        
        $rutaCompleta = public_path('fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo);
        
        if (file_exists($rutaCompleta)) {
            return asset('fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo);
        }
        
        return null;
    }

    /**
     * Obtener el nombre descriptivo del tipo de familiar
     * 
     * @return string
     */
    public function getTipoFamiliarNombre()
    {
        $tipos = [
            1 => 'Cónyuge',
            2 => 'Hijo/a',
            3 => 'Hijo/a con discapacidad',
            4 => 'Otro familiar a cargo',
        ];
        
        return $tipos[$this->TIPOFAMI] ?? 'Desconocido';
    }

    /**
     * Accessor para obtener el tipo de familiar como texto
     */
    public function getTipoFamiliarAttribute()
    {
        return $this->getTipoFamiliarNombre();
    }

    /**
     * Obtener el nombre descriptivo del tipo de escolaridad
     * 
     * @return string
     */
    public function getTipoEscolaridadNombre()
    {
        $tipos = [
            'P' => 'Primaria',
            'S' => 'Secundaria',
            'J' => 'Jardín/Inicial',
            'L' => 'Universitaria/Terciaria',
        ];
        
        return $tipos[$this->TIPOESCO] ?? 'No especificado';
    }

    /**
     * Accessor para obtener el tipo de escolaridad como texto
     */
    public function getNivelEducativoAttribute()
    {
        return $this->getTipoEscolaridadNombre();
    }

    /**
     * Contar hijos de un empleado
     * Incluye todos los tipos de familia: 1, 2, 3, 4
     * 
     * @param int $legajo
     * @return int
     */
    public static function contarHijos($legajo)
    {
        return self::join('in_maestro as m', 'm.legajo', '=', 'in_familia.LEGAJO')
            ->where('in_familia.LEGAJO', $legajo)
            ->whereIn('in_familia.TIPOFAMI', [1, 2, 3, 4])
            ->where('m.sexo', 1)
            ->count();
    }

    /**
     * Obtener todos los hijos de un empleado
     * Incluye todos los tipos de familia: 1, 2, 3, 4
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
            ->whereIn('in_familia.TIPOFAMI', [1, 2, 3, 4])
            ->where('m.sexo', 1)
            ->get();
    }

    /**
     * Obtener hijos para gestión de planillas
     * Incluye todos los campos necesarios y ordenados por fecha de nacimiento
     * 
     * @param int $legajo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function obtenerHijosParaPlanillas($legajo)
    {
        return self::porLegajo($legajo)
            ->soloHijos()
            ->orderBy('FECHA_NAC', 'asc')
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