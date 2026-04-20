<?php

namespace App\Http\Controllers;

use App\Mail\PerfilActualizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            
            // Ruta directa al directorio público
            $directorio = public_path('fotos-licencias/fotos-empleados');
            $rutaCompleta = $directorio . '/' . $nombreArchivo;
            $marcadorEliminada = $directorio . '/' . $legajo . '_eliminada.txt';
            
            // Asegurar que el directorio existe
            if (!file_exists($directorio)) {
                mkdir($directorio, 0775, true);
            }
            
            // Obtener el archivo subido
            $archivo = $request->file('foto');
            
            // Procesar la imagen y convertir a JPG
            $imagen = Image::read($archivo);
            
            // Redimensionar si es muy grande (mantener proporción, max 800px)
            $imagen->scaleDown(width: 800);
            
            // Guardar la imagen como JPG con calidad 85
            $imagen->toJpeg(quality: 85)->save($rutaCompleta);
            
            // Establecer permisos correctos
            chmod($rutaCompleta, 0664);
            
            // Eliminar el marcador si existe
            if (file_exists($marcadorEliminada)) {
                unlink($marcadorEliminada);
            }

            $urlFoto = asset('fotos-licencias/fotos-empleados/' . $nombreArchivo);

            $this->notificarFoto($urlFoto);

            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada correctamente',
                'url' => $urlFoto . '?t=' . time()
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

    private function notificarFoto(string $urlFoto): void
    {
        try {
            $empleado = DB::table('in_maestro')
                ->where('LEGAJO', Auth::user()->LEGAJO)
                ->first();

            if (!$empleado) {
                return;
            }

            Mail::to(config('mail.perfil_notificacion_to'))->send(new PerfilActualizado(
                nombre: $empleado->NOMBRE ?? '',
                legajo: (string) $empleado->LEGAJO,
                motivo: 'Actualización de foto de perfil',
                mensaje: 'El empleado subió una nueva foto de perfil.',
                imagen: $urlFoto,
            ));
        } catch (\Exception $e) {
            \Log::error('Error al enviar mail de notificación de foto: ' . $e->getMessage(), [
                'legajo' => Auth::user()->LEGAJO ?? null,
            ]);
        }
    }
}