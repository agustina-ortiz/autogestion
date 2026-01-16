<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AsignacionesFamiliaresController extends Controller
{
    /**
     * Subir archivo de asignaciones familiares vía AJAX
     */
    public function subirArchivo(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'index' => 'required|integer',
                'dnihijo' => 'required',
                'anio' => 'required',
                'periodo' => 'required'
            ]);

            $legajo = Auth::user()->LEGAJO;
            $index = $request->input('index');
            $dnihijo = $request->input('dnihijo');
            $anio = $request->input('anio');
            $periodo = $request->input('periodo');

            // Obtener el archivo
            $archivo = $request->file('archivo');
            $extension = $archivo->getClientOriginalExtension();
            
            // Nombre del archivo: LEGAJOAÑOPERIODO_DNIHIJO.extension
            $nombreArchivo = "{$legajo}{$anio}{$periodo}_{$dnihijo}.{$extension}";

            // Eliminar archivos previos de este hijo/a con diferentes extensiones
            $extensiones = ['jpg', 'jpeg', 'png', 'pdf'];
            foreach ($extensiones as $ext) {
                $pathAnterior = "asignaciones-familiares/{$legajo}{$anio}{$periodo}_{$dnihijo}.{$ext}";
                if (Storage::disk('public')->exists($pathAnterior)) {
                    Storage::disk('public')->delete($pathAnterior);
                    Log::info("Archivo anterior eliminado: {$pathAnterior}");
                }
            }

            // Guardar el nuevo archivo
            $path = $archivo->storeAs('asignaciones-familiares', $nombreArchivo, 'public');

            Log::info("Archivo guardado exitosamente", [
                'legajo' => $legajo,
                'index' => $index,
                'dnihijo' => $dnihijo,
                'path' => $path,
                'nombre' => $nombreArchivo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido correctamente',
                'filename' => $nombreArchivo,
                'path' => $path,
                'url' => Storage::url($path)
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

            // Eliminar archivos con cualquier extensión
            $extensiones = ['jpg', 'jpeg', 'png', 'pdf'];
            $eliminado = false;

            foreach ($extensiones as $ext) {
                $path = "asignaciones-familiares/{$legajo}{$anio}{$periodo}_{$dnihijo}.{$ext}";
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $eliminado = true;
                    Log::info("Archivo eliminado: {$path}");
                }
            }

            if ($eliminado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Archivo eliminado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún archivo para eliminar'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error("Error al eliminar archivo de asignaciones familiares", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}