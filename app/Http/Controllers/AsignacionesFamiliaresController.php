<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AsignacionesFamiliaresController extends Controller
{
    private function generarNombreArchivo($legajo, $anio, $periodo, $dnihijo)
    {
        $legajoFormateado = str_pad($legajo, 8, '0', STR_PAD_LEFT);
        $dniFormateado = str_pad($dnihijo, 8, '0', STR_PAD_LEFT);
        
        return "{$legajoFormateado}-{$anio}{$periodo}{$dniFormateado}.jpg";
    }

    /**
     * Subir archivo de asignaciones familiares vía AJAX
     */
    public function subirArchivo(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:jpg|max:5120',
                'index' => 'required|integer',
                'dnihijo' => 'required',
                'anio' => 'required',
                'periodo' => 'required'
            ], [
                'archivo.mimes' => 'El archivo debe ser una imagen JPG',
                'archivo.max' => 'El archivo no debe superar los 5MB',
            ]);

            $legajo = Auth::user()->LEGAJO;
            $index = $request->input('index');
            $dnihijo = $request->input('dnihijo');
            $anio = $request->input('anio');
            $periodo = $request->input('periodo');

            // Obtener el archivo
            $archivo = $request->file('archivo');
            
            // Generar nombre según nueva nomenclatura
            $nombreArchivo = $this->generarNombreArchivo($legajo, $anio, $periodo, $dnihijo);
            
            // Crear directorio si no existe
            $directorio = public_path('img/ddjj_fami');
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
                Log::info("Directorio creado: {$directorio}");
            }
            
            $rutaCompleta = "{$directorio}/{$nombreArchivo}";
            
            // Eliminar archivo anterior si existe
            if (File::exists($rutaCompleta)) {
                File::delete($rutaCompleta);
                Log::info("Archivo anterior eliminado: {$nombreArchivo}");
            }
            
            // Mover el archivo a la nueva ubicación
            $archivo->move($directorio, $nombreArchivo);

            Log::info("Archivo guardado exitosamente", [
                'legajo' => $legajo,
                'legajo_formateado' => str_pad($legajo, 8, '0', STR_PAD_LEFT),
                'index' => $index,
                'dnihijo' => $dnihijo,
                'dni_formateado' => str_pad($dnihijo, 8, '0', STR_PAD_LEFT),
                'anio' => $anio,
                'periodo' => $periodo,
                'nombre' => $nombreArchivo,
                'ruta' => $rutaCompleta
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido correctamente',
                'filename' => $nombreArchivo,
                'url' => asset("img/ddjj_fami/{$nombreArchivo}")
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Error de validación al subir archivo", [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Error al subir archivo de asignaciones familiares", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar archivo de asignaciones familiares vía AJAX
     */
    public function eliminarArchivo(Request $request)
    {
        try {
            $request->validate([
                'index' => 'required|integer',
                'dnihijo' => 'required',
                'anio' => 'required',
                'periodo' => 'required'
            ]);

            $legajo = Auth::user()->LEGAJO;
            $dnihijo = $request->input('dnihijo');
            $anio = $request->input('anio');
            $periodo = $request->input('periodo');

            // Generar nombre según nueva nomenclatura
            $nombreArchivo = $this->generarNombreArchivo($legajo, $anio, $periodo, $dnihijo);
            $rutaCompleta = public_path("img/ddjj_fami/{$nombreArchivo}");

            if (File::exists($rutaCompleta)) {
                File::delete($rutaCompleta);
                
                Log::info("Archivo eliminado exitosamente", [
                    'legajo' => $legajo,
                    'dnihijo' => $dnihijo,
                    'anio' => $anio,
                    'periodo' => $periodo,
                    'archivo' => $nombreArchivo
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo eliminado correctamente'
                ]);
            } else {
                Log::warning("Intento de eliminar archivo inexistente", [
                    'archivo' => $nombreArchivo,
                    'ruta' => $rutaCompleta
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún archivo para eliminar'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error("Error al eliminar archivo de asignaciones familiares", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}