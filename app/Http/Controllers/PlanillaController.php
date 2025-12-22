<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Planilla;

class PlanillaController extends Controller
{
    // NO usar __construct con middleware en Laravel 11

    public function subir(Request $request)
    {
        Log::info('===== PETICIÓN RECIBIDA =====', [
            'method' => $request->method(),
            'has_file' => $request->hasFile('foto'),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id() ?? 'no-auth'
        ]);

        if (!Auth::check()) {
            Log::error('Usuario NO autenticado');
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado. Por favor, recarga la página.'
            ], 401);
        }

        try {
            // Validar
            $request->validate([
                'foto' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'dni' => 'required|numeric',
                'planilla' => 'required|numeric',
                'anio' => 'required|numeric',
            ], [
                'foto.required' => 'Debe seleccionar un archivo',
                'foto.file' => 'Debe seleccionar un archivo válido',
                'foto.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
                'foto.max' => 'El archivo no debe superar 10MB',
            ]);

            $legajo = Auth::user()->LEGAJO;
            $dni = $request->dni;
            $planilla = $request->planilla;
            $anio = $request->anio;

            Log::info('Datos validados', [
                'legajo' => $legajo,
                'dni' => $dni,
                'planilla' => $planilla,
                'anio' => $anio
            ]);

            DB::connection('mysql')->beginTransaction();

            $file = $request->file('foto');
            $extension = strtolower($file->getClientOriginalExtension());
            
            Log::info('Archivo recibido', [
                'name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            // Determinar extensión final
            $extensionFinal = ($extension === 'pdf') ? 'pdf' : 'jpg';

            // Generar nombre del archivo
            $fileName = 'planilla_' . 
                       str_pad($dni, 8, '0', STR_PAD_LEFT) . '_' .
                       $planilla . '_' .
                       $anio . '_' .
                       time() . '.' . 
                       $extensionFinal;

            Log::info('Nombre generado', ['fileName' => $fileName]);

            // Guardar archivo en storage/app/public/planillas
            if ($extension === 'pdf') {
                Log::info('Guardando PDF en storage/app/public/planillas');
                
                // Guardar PDF directamente
                $path = $file->storeAs('planillas', $fileName, 'public');
                
                Log::info('PDF guardado', [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path)
                ]);
                
            } else {
                Log::info('Procesando imagen');
                
                try {
                    // Procesar con Intervention Image
                    $image = Image::read($file->getRealPath());
                    $image->toJpeg(90);
                    $imageData = (string) $image->encode('jpg', 90);
                    
                    // Guardar en storage/app/public/planillas
                    Storage::disk('public')->put('planillas/' . $fileName, $imageData);
                    
                    $path = 'planillas/' . $fileName;
                    
                    Log::info('Imagen procesada y guardada', [
                        'path' => $path,
                        'full_path' => storage_path('app/public/' . $path)
                    ]);
                    
                } catch (\Exception $e) {
                    Log::warning('Error con Intervention Image, guardando sin procesar', [
                        'error' => $e->getMessage()
                    ]);
                    
                    // Fallback: guardar sin procesar
                    $path = $file->storeAs('planillas', $fileName, 'public');
                    
                    Log::info('Imagen guardada sin procesar', ['path' => $path]);
                }
            }

            // Verificar que se guardó en storage/app/public/planillas
            if (!Storage::disk('public')->exists('planillas/' . $fileName)) {
                throw new \Exception('El archivo no se guardó correctamente en storage/app/public/planillas');
            }

            $filesize = Storage::disk('public')->size('planillas/' . $fileName);
            
            if ($filesize === 0) {
                throw new \Exception('El archivo guardado está vacío');
            }

            Log::info('Archivo verificado en storage', [
                'size' => $filesize,
                'path' => 'storage/app/public/planillas/' . $fileName,
                'url' => Storage::disk('public')->url('planillas/' . $fileName)
            ]);

            // Eliminar registro previo si existe
            $eliminados = Planilla::porLegajo($legajo)
                ->porPeriodo($anio, $planilla)
                ->porDni($dni)
                ->delete();

            Log::info('Registros previos eliminados', ['count' => $eliminados]);

            // Crear nuevo registro
            Planilla::create([
                'legajo'      => $legajo,
                'anio'        => $anio,
                'planilla'    => $planilla,
                'dni'         => $dni,
                'fecha'       => now()->toDateString(),
                'confirmada'  => false,
            ]);

            DB::connection('mysql')->commit();

            Log::info('===== PLANILLA SUBIDA EXITOSAMENTE =====', [
                'archivo' => $fileName,
                'size' => $filesize,
                'ubicacion' => 'storage/app/public/planillas/' . $fileName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Planilla subida exitosamente'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::connection('mysql')->rollBack();
            
            Log::error('Error de validación', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();

            Log::error('===== ERROR AL SUBIR PLANILLA =====', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}