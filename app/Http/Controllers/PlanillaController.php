<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Planilla;

class PlanillaController extends Controller
{
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
                'foto' => 'required|file|mimes:jpg|max:10240',
                'dni' => 'required|numeric',
                'planilla' => 'required|numeric',
                'anio' => 'required|numeric',
            ], [
                'foto.required' => 'Debe seleccionar un archivo',
                'foto.file' => 'Debe seleccionar un archivo válido',
                'foto.mimes' => 'Solo se permiten archivos JPG',
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

            // Generar nombre del archivo: {dni}{planilla}-{año}.{extensión}
            // Ejemplo: 123456781-2025.jpg o 123456782-2024.pdf
            $dniPadded = str_pad($dni, 8, '0', STR_PAD_LEFT);
            $fileName = $dniPadded . $planilla . '-' . $anio . '.' . $extensionFinal;

            Log::info('Nombre generado', ['fileName' => $fileName]);

            // Ruta completa en public
            $directorioDestino = public_path('fotos-licencias/fotos-empleados/planillas');
            
            // Crear directorio si no existe
            if (!file_exists($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
                Log::info('Directorio creado', ['path' => $directorioDestino]);
            }

            $rutaCompleta = $directorioDestino . '/' . $fileName;

            // Eliminar archivo anterior si existe (mismo DNI, planilla y año)
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
                Log::info('Archivo anterior eliminado', ['path' => $rutaCompleta]);
            }

            // Guardar archivo según el tipo
            if ($extension === 'pdf') {
                Log::info('Guardando PDF');
                
                // Mover PDF directamente
                $file->move($directorioDestino, $fileName);
                
                Log::info('PDF guardado', [
                    'path' => $rutaCompleta
                ]);
                
            } else {
                Log::info('Procesando imagen');
                
                try {
                    // Procesar con Intervention Image y convertir a JPG
                    $image = Image::read($file->getRealPath());
                    $image->toJpeg(90);
                    $image->save($rutaCompleta);
                    
                    Log::info('Imagen procesada y guardada', [
                        'path' => $rutaCompleta
                    ]);
                    
                } catch (\Exception $e) {
                    Log::warning('Error con Intervention Image, guardando sin procesar', [
                        'error' => $e->getMessage()
                    ]);
                    
                    // Fallback: guardar sin procesar
                    $file->move($directorioDestino, $fileName);
                    
                    Log::info('Imagen guardada sin procesar', ['path' => $rutaCompleta]);
                }
            }

            // Verificar que se guardó correctamente
            if (!file_exists($rutaCompleta)) {
                throw new \Exception('El archivo no se guardó correctamente');
            }

            $filesize = filesize($rutaCompleta);
            
            if ($filesize === 0) {
                throw new \Exception('El archivo guardado está vacío');
            }

            // Establecer permisos apropiados
            chmod($rutaCompleta, 0644);

            Log::info('Archivo verificado', [
                'size' => $filesize,
                'path' => $rutaCompleta,
                'url' => asset('fotos-licencias/fotos-empleados/planillas/' . $fileName)
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
                'ubicacion' => 'public/fotos-licencias/fotos-empleados/planillas/' . $fileName
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