<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class PerfilFotoController extends Controller
{
    public function subirFoto(Request $request)
    {
        try {
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'foto.required' => 'Debe seleccionar una imagen.',
                'foto.image' => 'El archivo debe ser una imagen.',
                'foto.mimes' => 'Solo se permiten imágenes JPG, PNG o GIF.',
                'foto.max' => 'La imagen no puede superar los 2MB.'
            ]);

            $legajo = str_pad(Auth::user()->LEGAJO, 8, '0', STR_PAD_LEFT);
            $nombreArchivo = $legajo . '.jpg';
            $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
            
            // Obtener el archivo subido
            $archivo = $request->file('foto');
            
            // Procesar la imagen y convertir a JPG
            $imagen = Image::read($archivo);
            
            // Redimensionar si es muy grande (mantener proporción, max 800px)
            $imagen->scaleDown(width: 800);
            
            // Guardar como JPG
            $rutaCompleta = storage_path('app/public/fotos-empleados/' . $nombreArchivo);
            
            // Asegurar que el directorio existe
            $directorio = dirname($rutaCompleta);
            if (!file_exists($directorio)) {
                mkdir($directorio, 0775, true);
            }
            
            // Guardar la imagen como JPG con calidad 85
            $imagen->toJpeg(quality: 85)->save($rutaCompleta);
            
            // Eliminar el marcador si existe
            if (Storage::disk('public')->exists($marcadorEliminada)) {
                Storage::disk('public')->delete($marcadorEliminada);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada correctamente',
                'url' => asset('storage/fotos-empleados/' . $nombreArchivo) . '?t=' . time()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error al subir foto de perfil: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la imagen. Por favor, intente con otra imagen.'
            ], 500);
        }
    }
}