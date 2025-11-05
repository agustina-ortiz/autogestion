<?php
// app/Livewire/AsignacionesFamiliares.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Familia;
use Carbon\Carbon;

class AsignacionesFamiliares extends Component
{
    use WithFileUploads;

    public $hijos = [];
    public $formularios = [];
    public $archivos = []; // nuevo arreglo temporal para los archivos seleccionados
    public $mismoProgenitor = [];
    public $anio;
    public $periodo;

    public $tiposAdjunto = [
        1 => 'Recibo de Sueldo',
        2 => 'Constancia AFIP',
        3 => 'Certificación ANSES',
        4 => 'No tengo acceso a esa información'
    ];
    
    public function mount()
    {
        $this->anio = Carbon::now()->year;
        $this->periodo = Carbon::now()->month;
        $this->cargarHijos();
        $this->inicializarFormularios();
    }

    public function cargarHijos()
    {
        $legajo = Auth::user()->LEGAJO;
        
        // Obtener hijos usando el modelo Familia
        $hijosData = Familia::obtenerHijos($legajo);
        
        $this->hijos = $hijosData->map(function($hijo, $index) {
            return [
                'nombre' => $hijo->nombre,
                'dni' => $hijo->dni,
                'fecha_nac' => $hijo->fecha_nac,
                'index' => $index
            ];
        })->toArray();
    }

    public function inicializarFormularios()
    {
        $legajo = Auth::user()->LEGAJO;
        
        foreach ($this->hijos as $index => $hijo) {
            // Buscar si ya existe información guardada
            $ddjj = DB::connection('mysql')
                ->table('in_ddjj_fami')
                ->where('legajo', $legajo)
                ->where('anio', $this->anio)
                ->where('periodo', $this->periodo)
                ->where('dnihijo', $hijo['dni'])
                ->first();
            
            $this->formularios[$index] = [
                'dnihijo' => $hijo['dni'],
                'nombre' => $hijo['nombre'],
                'fecha_nac' => $hijo['fecha_nac'],
                'nombrepadre' => $ddjj->nombrepadre ?? '',
                'dnipadre' => $ddjj->dnipadre ?? '',
                'cuilpadre' => $ddjj->cuilpadre ?? '',
                'tipoadjunto' => $ddjj->tipoadjunto ?? '',
                'archivo_actual' => $ddjj->tipoadjunto ?? null,
                'nuevo_archivo' => null,
                'respuesta' => $ddjj->respuesta ?? null,
                'ok' => $ddjj->ok ?? 0
            ];
            
            $this->mismoProgenitor[$index] = false;
        }
    }

    public function updatedMismoProgenitor($value, $index)
    {
        if ($value && $index > 0) {
            // Copiar datos del progenitor anterior
            $this->formularios[$index]['nombrepadre'] = $this->formularios[$index - 1]['nombrepadre'];
            $this->formularios[$index]['dnipadre'] = $this->formularios[$index - 1]['dnipadre'];
            $this->formularios[$index]['cuilpadre'] = $this->formularios[$index - 1]['cuilpadre'];
        }
    }

    public function guardarTodosLosFormularios()
    {
        // Validar todos los formularios
        $reglas = [];
        $mensajes = [];
        
        foreach ($this->formularios as $index => $formulario) {
            $reglas["formularios.{$index}.nombrepadre"] = 'required|string|max:255';
            $reglas["formularios.{$index}.dnipadre"] = 'required|digits:8';
            $reglas["formularios.{$index}.cuilpadre"] = 'required|digits:11';
            $reglas["formularios.{$index}.tipoadjunto"] = 'required';
            
            $mensajes["formularios.{$index}.nombrepadre.required"] = "El nombre del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.dnipadre.required"] = "El DNI del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.dnipadre.digits"] = "El DNI debe tener 8 dígitos (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.cuilpadre.required"] = "El CUIL del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.cuilpadre.digits"] = "El CUIL debe tener 11 dígitos (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.tipoadjunto.required"] = "Debe seleccionar el tipo de adjunto (Hijo/a " . ($index + 1) . ")";
        }
        
        $this->validate($reglas, $mensajes);
        
        $legajo = Auth::user()->LEGAJO;
        
        // Guardar cada formulario
        foreach ($this->formularios as $index => $formulario) {
            // Guardar archivo si hay uno nuevo en archivos[$index]
            if (isset($this->archivos[$index]) && $this->archivos[$index]) {
                $nombreArchivo = "{$legajo}_{$this->anio}_{$this->periodo}_{$formulario['dnihijo']}.{$this->archivos[$index]->extension()}";
                $this->archivos[$index]->storeAs('asignaciones-familiares', $nombreArchivo, 'public');
            }

            // Verificar si existe el registro
            $existe = DB::connection('mysql')
                ->table('in_ddjj_fami')
                ->where('legajo', $legajo)
                ->where('anio', $this->anio)
                ->where('periodo', $this->periodo)
                ->where('dnihijo', $formulario['dnihijo'])
                ->exists();

            $datos = [
                'legajo' => $legajo,
                'anio' => $this->anio,
                'periodo' => $this->periodo,
                'dnihijo' => $formulario['dnihijo'],
                'nombre' => $formulario['nombre'],
                'fecha_nac' => $formulario['fecha_nac'],
                'fecha' => Carbon::now()->format('Y-m-d'),
                'dnipadre' => $formulario['dnipadre'],
                'cuilpadre' => $formulario['cuilpadre'],
                'nombrepadre' => $formulario['nombrepadre'],
                'tipoadjunto' => (int) $formulario['tipoadjunto'],
                'ok' => 0
            ];

            if ($existe) {
                DB::connection('mysql')
                    ->table('in_ddjj_fami')
                    ->where('legajo', $legajo)
                    ->where('anio', $this->anio)
                    ->where('periodo', $this->periodo)
                    ->where('dnihijo', $formulario['dnihijo'])
                    ->update($datos);
            } else {
                DB::connection('mysql')
                    ->table('in_ddjj_fami')
                    ->insert($datos);
            }
        }

        session()->flash('success', 'Toda la información ha sido guardada correctamente');
        
        // Recargar datos
        $this->inicializarFormularios();
        
        // Limpiar archivos temporales
        $this->archivos = [];
    }

    public function updatedArchivos($value, $key)
    {
        $index = $key;

        if (isset($this->archivos[$index]) && $this->archivos[$index]->isValid()) {
            $form = $this->formularios[$index];

            // SIN timestamp para que coincida con la búsqueda
            $nombreArchivo = auth()->user()->LEGAJO . '_' . $this->anio . '_' . $this->periodo . '_' . $form['dnihijo'] . '.' . $this->archivos[$index]->getClientOriginalExtension();

            // Guardar en storage/app/public/asignaciones-familiares
            $this->archivos[$index]->storeAs('asignaciones-familiares', $nombreArchivo, 'public');

            $this->formularios[$index]['archivo_actual'] = $this->formularios[$index]['tipoadjunto'];
            
            $this->reset('archivos');
        }
    }

    public function render()
    {
        return view('livewire.asignaciones-familiares')->layout('components.layouts.autogestion');
    }
}
