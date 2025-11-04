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

    public function guardarFormulario($index)
    {
        $this->validate([
            "formularios.{$index}.nombrepadre" => 'required|string|max:255',
            "formularios.{$index}.dnipadre" => 'required|digits:8',
            "formularios.{$index}.cuilpadre" => 'required|digits:11',
            "formularios.{$index}.tipoadjunto" => 'required',
            "formularios.{$index}.nuevo_archivo" => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ], [
            "formularios.{$index}.nombrepadre.required" => 'El nombre del progenitor es obligatorio',
            "formularios.{$index}.dnipadre.required" => 'El DNI del progenitor es obligatorio',
            "formularios.{$index}.dnipadre.digits" => 'El DNI debe tener 8 dígitos',
            "formularios.{$index}.cuilpadre.required" => 'El CUIL del progenitor es obligatorio',
            "formularios.{$index}.cuilpadre.digits" => 'El CUIL debe tener 11 dígitos',
            "formularios.{$index}.tipoadjunto.required" => 'Debe seleccionar el tipo de adjunto',
            "formularios.{$index}.nuevo_archivo.max" => 'El archivo no debe superar los 5MB'
        ]);

        $formulario = $this->formularios[$index];
        $legajo = Auth::user()->LEGAJO;
        
        // Guardar archivo si hay uno nuevo
        if ($formulario['nuevo_archivo']) {
            $nombreArchivo = "{$legajo}_{$this->anio}_{$this->periodo}_{$formulario['dnihijo']}.{$formulario['nuevo_archivo']->extension()}";
            $formulario['nuevo_archivo']->storeAs('asignaciones-familiares', $nombreArchivo, 'public');
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
            'tipoadjunto' => (int) $formulario['tipoadjunto'], // Convertir a entero
            'ok' => 0 // Pendiente de revisión
        ];

        if ($existe) {
            // Actualizar
            DB::connection('mysql')
                ->table('in_ddjj_fami')
                ->where('legajo', $legajo)
                ->where('anio', $this->anio)
                ->where('periodo', $this->periodo)
                ->where('dnihijo', $formulario['dnihijo'])
                ->update($datos);
        } else {
            // Insertar
            DB::connection('mysql')
                ->table('in_ddjj_fami')
                ->insert($datos);
        }

        // Actualizar datos en el formulario
        $this->formularios[$index]['archivo_actual'] = $formulario['tipoadjunto'];
        $this->formularios[$index]['nuevo_archivo'] = null;

        session()->flash('success', 'Información guardada correctamente');
        
        // Recargar datos
        $this->inicializarFormularios();
    }

    public function updatedArchivos($value, $key)
    {
        // $key tiene el índice del formulario que cambió
        $index = $key;

        if (isset($this->archivos[$index]) && $this->archivos[$index]->isValid()) {
            $form = $this->formularios[$index];

            $nombreArchivo = auth()->user()->LEGAJO . '_' . $this->anio . '_' . $this->periodo . '_' . $form['dnihijo'] . '_' . now()->format('Ymd_His') . '.' . $this->archivos[$index]->getClientOriginalExtension();

            // Guardar el archivo en storage/app/public/asignaciones-familiares
            $this->archivos[$index]->storeAs('public/asignaciones-familiares', $nombreArchivo);

            // Guardar el nombre del archivo en el formulario correspondiente
            $this->formularios[$index]['archivo_actual'] = $nombreArchivo;

            // Limpiar el input temporal para evitar que se repita el archivo en otros hijos
            $this->reset('archivos');
        }
    }

    public function render()
    {
        return view('livewire.asignaciones-familiares')->layout('components.layouts.autogestion');
    }
}
