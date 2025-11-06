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
    public $archivos = [];
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
            $this->formularios[$index]['nombrepadre'] = $this->formularios[$index - 1]['nombrepadre'];
            $this->formularios[$index]['dnipadre'] = $this->formularios[$index - 1]['dnipadre'];
            $this->formularios[$index]['cuilpadre'] = $this->formularios[$index - 1]['cuilpadre'];
        }
    }

    // Nuevo método: cuando cambia el tipo de adjunto
    public function updatedFormularios($value, $key)
    {
        // Detectar si cambió el tipoadjunto
        if (strpos($key, '.tipoadjunto') !== false) {
            preg_match('/(\d+)\.tipoadjunto/', $key, $matches);
            $index = $matches[1];
            
            // Si selecciona "No tengo acceso a esa información" (opción 4)
            if ($value == 4) {
                // Limpiar archivo temporal
                if (isset($this->archivos[$index])) {
                    unset($this->archivos[$index]);
                }
                
                // Eliminar archivo físico si existe
                $this->eliminarArchivoFisico($index);
                
                // Marcar que no hay archivo actual
                $this->formularios[$index]['archivo_actual'] = null;
            }
        }
    }

    // Nuevo método: eliminar archivo seleccionado
    public function eliminarArchivo($index)
    {
        // Limpiar archivo temporal
        if (isset($this->archivos[$index])) {
            unset($this->archivos[$index]);
        }
        
        // Eliminar archivo físico
        $this->eliminarArchivoFisico($index);
        
        // Actualizar estado
        $this->formularios[$index]['archivo_actual'] = null;
        
        session()->flash('success', 'Archivo eliminado correctamente');
    }

    // Método auxiliar para eliminar archivo físico
    private function eliminarArchivoFisico($index)
    {
        $nombreArchivo = auth()->user()->LEGAJO . '' . $this->anio . '' . $this->periodo . '_' . $this->formularios[$index]['dnihijo'];
        $extensiones = ['jpg', 'jpeg', 'png', 'pdf'];
        
        foreach($extensiones as $ext) {
            $path = "asignaciones-familiares/{$nombreArchivo}.{$ext}";
            if(Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public function guardarTodosLosFormularios()
    {
        $reglas = [];
        $mensajes = [];
        
        foreach ($this->formularios as $index => $formulario) {
            $reglas["formularios.{$index}.nombrepadre"] = 'required|string|max:255';
            $reglas["formularios.{$index}.dnipadre"] = 'required|digits:8';
            $reglas["formularios.{$index}.cuilpadre"] = 'required|digits:11';
            $reglas["formularios.{$index}.tipoadjunto"] = 'required';
            
            // Validar archivo solo si NO es la opción "No tengo acceso"
            if ($formulario['tipoadjunto'] != 4) {
                // Debe haber un archivo nuevo O un archivo actual
                if (!isset($this->archivos[$index]) && !$formulario['archivo_actual']) {
                    $reglas["formularios.{$index}.archivo_requerido"] = 'required';
                    $mensajes["formularios.{$index}.archivo_requerido.required"] = "Debe cargar un archivo para el Hijo/a " . ($index + 1);
                }
            }
            
            $mensajes["formularios.{$index}.nombrepadre.required"] = "El nombre del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.dnipadre.required"] = "El DNI del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.dnipadre.digits"] = "El DNI debe tener 8 dígitos (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.cuilpadre.required"] = "El CUIL del progenitor es obligatorio (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.cuilpadre.digits"] = "El CUIL debe tener 11 dígitos (Hijo/a " . ($index + 1) . ")";
            $mensajes["formularios.{$index}.tipoadjunto.required"] = "Debe seleccionar el tipo de adjunto (Hijo/a " . ($index + 1) . ")";
        }
        
        $this->validate($reglas, $mensajes);
        
        $legajo = Auth::user()->LEGAJO;
        
        foreach ($this->formularios as $index => $formulario) {
            // Guardar archivo si hay uno nuevo
            if (isset($this->archivos[$index]) && $this->archivos[$index]) {
                $nombreArchivo = "{$legajo}{$this->anio}{$this->periodo}_{$formulario['dnihijo']}.{$this->archivos[$index]->extension()}";
                $this->archivos[$index]->storeAs('asignaciones-familiares', $nombreArchivo, 'public');
            }

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
        
        $this->inicializarFormularios();
        $this->archivos = [];
    }

    public function updatedArchivos($value, $key)
    {
        $index = $key;

        if (isset($this->archivos[$index]) && $this->archivos[$index]->isValid()) {
            $form = $this->formularios[$index];

            $nombreArchivo = auth()->user()->LEGAJO . '' . $this->anio . '' . $this->periodo . '_' . $form['dnihijo'] . '.' . $this->archivos[$index]->getClientOriginalExtension();

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