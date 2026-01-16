<?php
// app/Livewire/AsignacionesFamiliares.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Familia;
use Carbon\Carbon;

class AsignacionesFamiliares extends Component
{
    public $hijos = [];
    public $formularios = [];
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
        $this->periodo = $this->calcularPeriodo();
        $this->cargarHijos();
        $this->inicializarFormularios();
    }

    // Método para calcular el período según el semestre
    private function calcularPeriodo()
    {
        $mes = Carbon::now()->month;
        return $mes <= 6 ? 1 : 2;
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
            // Buscar DDJJ del período actual
            $ddjj = DB::connection('mysql')
                ->table('in_ddjj_fami')
                ->where('legajo', $legajo)
                ->where('anio', $this->anio)
                ->where('periodo', $this->periodo)
                ->where('dnihijo', $hijo['dni'])
                ->first();
            
            // Verificar si existe archivo físico
            $archivoExiste = $this->verificarArchivoExiste($hijo['dni']);
            
            $this->formularios[$index] = [
                'dnihijo' => $hijo['dni'],
                'nombre' => $hijo['nombre'],
                'fecha_nac' => $hijo['fecha_nac'],
                'nombrepadre' => $ddjj->nombrepadre ?? '',
                'dnipadre' => $ddjj->dnipadre ?? '',
                'cuilpadre' => $ddjj->cuilpadre ?? '',
                'tipoadjunto' => $ddjj->tipoadjunto ?? '',
                'archivo_actual' => $archivoExiste ? ($ddjj->tipoadjunto ?? null) : null,
                'respuesta' => $ddjj->respuesta ?? null,
                'ok' => $ddjj->ok ?? 0
            ];
            
            $this->mismoProgenitor[$index] = false;
        }
    }

    /**
     * Verificar si existe archivo físico para este hijo
     */
    private function verificarArchivoExiste($dnihijo)
    {
        $legajo = Auth::user()->LEGAJO;
        $nombreArchivo = "{$legajo}{$this->anio}{$this->periodo}_{$dnihijo}";
        $extensiones = ['jpg', 'jpeg', 'png', 'pdf'];
        
        foreach($extensiones as $ext) {
            $path = "asignaciones-familiares/{$nombreArchivo}.{$ext}";
            if(Storage::disk('public')->exists($path)) {
                return true;
            }
        }
        
        return false;
    }

    public function validarCamposBasicos()
    {
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
        
        try {
            $this->validate($reglas, $mensajes);
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Los errores ya están en el componente
            return false;
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

    public function updatedFormularios($value, $key)
    {
        if (strpos($key, '.tipoadjunto') !== false) {
            preg_match('/(\d+)\.tipoadjunto/', $key, $matches);
            $index = $matches[1];
            
            // Si selecciona "No tengo acceso", eliminar archivo
            if ($value == 4) {
                $this->eliminarArchivoJS($index);
            }
        }
    }

    /**
     * Método llamado desde JavaScript después de eliminar archivo
     */
    public function archivoEliminadoJS($index)
    {
        $this->formularios[$index]['archivo_actual'] = null;
        $this->dispatch('mostrarMensaje', mensaje: 'Archivo eliminado correctamente', tipo: 'success');
    }

    /**
     * Método llamado desde JavaScript después de subir archivo
     */
    public function archivoSubidoJS($index)
    {
        // Verificar que el archivo existe
        $archivoExiste = $this->verificarArchivoExiste($this->formularios[$index]['dnihijo']);
        
        if ($archivoExiste) {
            $this->formularios[$index]['archivo_actual'] = $this->formularios[$index]['tipoadjunto'];
            $this->dispatch('mostrarMensaje', mensaje: 'Archivo subido correctamente', tipo: 'success');
        }
    }

    /**
     * Método para eliminar archivo (llamado desde JavaScript)
     */
    public function eliminarArchivoJS($index)
    {
        // Este método solo actualiza el estado en el componente
        // La eliminación física se hace vía AJAX
        $this->formularios[$index]['archivo_actual'] = null;
    }

    public function guardarTodosLosFormularios()
    {
        // Solo validar archivos (los campos básicos ya fueron validados)
        foreach ($this->formularios as $index => $formulario) {
            if ($formulario['tipoadjunto'] != 4) {
                $archivoFisicoExiste = $this->verificarArchivoExiste($formulario['dnihijo']);
                
                if (!$archivoFisicoExiste) {
                    $this->addError("formularios.{$index}.archivo_requerido", "Debe cargar un archivo para el Hijo/a " . ($index + 1));
                    return;
                }
            }
        }
        
        $legajo = Auth::user()->LEGAJO;
        
        foreach ($this->formularios as $index => $formulario) {
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
    }

    public function render()
    {
        return view('livewire.asignaciones-familiares')->layout('components.layouts.autogestion');
    }
}