<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Perfil extends Component
{
    use WithFileUploads;

    public $nombre = '';
    public $domicilio = '';
    public $telefono = '';
    public $mail = '';
    public $foto;
    public $nuevaFoto;
    public $fotoActualUrl;
    public $eliminarFotoFlag = false; // Flag para marcar si se debe eliminar la foto
    
    public function mount()
    {
        try {
            $empleado = DB::table('in_maestro')
                ->where('LEGAJO', Auth::user()->LEGAJO)
                ->first();
            
            if ($empleado) {
                $this->nombre = $empleado->NOMBRE ?? '';
                $this->domicilio = $empleado->DOMICILIO ?? '';
                $this->telefono = $empleado->TELEFONO ?? '';
                $this->mail = $empleado->MAIL ?? '';
                
                // Cargar foto actual
                $this->cargarFotoActual();
            } else {
                session()->flash('error', 'No se encontraron datos del empleado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los datos: ' . $e->getMessage());
        }
    }

   public function cargarFotoActual()
    {
        $legajo = str_pad(Auth::user()->LEGAJO, 8, '0', STR_PAD_LEFT);
        $nombreArchivo = $legajo . '.jpg';
        $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
        
        // Si existe el marcador de foto eliminada, mostrar imagen por defecto
        if (Storage::disk('public')->exists($marcadorEliminada)) {
            $this->fotoActualUrl = asset('images/no-foto.png');
            return;
        }
        
        // Primero verificar si existe en storage local
        if (Storage::disk('public')->exists('fotos-empleados/' . $nombreArchivo)) {
            $this->fotoActualUrl = asset('storage/fotos-empleados/' . $nombreArchivo);
        } else {
            // Si no existe localmente, buscar en el servidor remoto
            $fotoUrl = 'https://autogestion.mercedes.gob.ar/fotos-licencias/fotos-empleados/' . $legajo . '.jpg';
            $tieneFoto = is_array(@getimagesize($fotoUrl));
            
            if ($tieneFoto) {
                $this->fotoActualUrl = $fotoUrl;
            } else {
                $this->fotoActualUrl = asset('images/no-foto.png');
            }
        }
    }

    public function eliminarFoto()
    {
        // Solo marcar para eliminar, no eliminar aún
        $this->eliminarFotoFlag = true;
        $this->nuevaFoto = null;
        $this->fotoActualUrl = asset('images/no-foto.png');
        
        // NO mostrar mensaje aquí, solo cambiar la vista previa
    }

    public function updatedNuevaFoto()
    {
        $this->validate([
            'nuevaFoto' => 'image|max:2048', // 2MB máximo
        ], [
            'nuevaFoto.image' => 'El archivo debe ser una imagen.',
            'nuevaFoto.max' => 'La imagen no puede superar los 2MB.',
        ]);
        
        // Si selecciona una nueva foto, cancelar el flag de eliminación
        $this->eliminarFotoFlag = false;
    }
    
    public function rules()
    {
        return [
            'domicilio' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'mail' => 'nullable|email|max:100',
            'nuevaFoto' => 'nullable|image|max:2048',
        ];
    }
    
    public function messages()
    {
        return [
            'mail.email' => 'El formato del correo electrónico no es válido.',
            'telefono.max' => 'El teléfono no puede exceder los 50 caracteres.',
            'domicilio.max' => 'El domicilio no puede exceder los 255 caracteres.',
            'mail.max' => 'El correo no puede exceder los 100 caracteres.',
            'nuevaFoto.image' => 'El archivo debe ser una imagen.',
            'nuevaFoto.max' => 'La imagen no puede superar los 2MB.',
        ];
    }
    
   public function save()
    {
        // Validar los datos
        $this->validate();
        
        try {
            $cambiosRealizados = false;
            $mensajesCambios = [];

            // Preparar datos para actualizar (solo si tienen valor)
            $datosActualizar = [];
            
            if ($this->domicilio !== null && $this->domicilio !== '') {
                $datosActualizar['DOMICILIO'] = $this->domicilio;
            }
            
            if ($this->telefono !== null && $this->telefono !== '') {
                $datosActualizar['TELEFONO'] = $this->telefono;
            }
            
            if ($this->mail !== null && $this->mail !== '') {
                $datosActualizar['MAIL'] = $this->mail;
            }

            // Actualizar datos de contacto solo si hay datos para actualizar
            if (!empty($datosActualizar)) {
                $affected = DB::table('in_maestro')
                    ->where('LEGAJO', Auth::user()->LEGAJO)
                    ->update($datosActualizar);

                if ($affected > 0) {
                    $cambiosRealizados = true;
                    $mensajesCambios[] = 'datos de contacto';
                }
            }

            // Procesar eliminación de foto si está marcada
            if ($this->eliminarFotoFlag) {
                $legajo = str_pad(Auth::user()->LEGAJO, 8, '0', STR_PAD_LEFT);
                $nombreArchivo = $legajo . '.jpg';
                $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
                
                // Eliminar de storage local si existe
                if (Storage::disk('public')->exists('fotos-empleados/' . $nombreArchivo)) {
                    Storage::disk('public')->delete('fotos-empleados/' . $nombreArchivo);
                }
                
                // Crear archivo marcador para indicar que la foto fue eliminada
                Storage::disk('public')->put($marcadorEliminada, 'eliminada');
                
                $cambiosRealizados = true;
                $mensajesCambios[] = 'foto eliminada';
                $this->eliminarFotoFlag = false;
            }

            // Si hay una nueva foto, guardarla
            if ($this->nuevaFoto) {
                $legajo = str_pad(Auth::user()->LEGAJO, 8, '0', STR_PAD_LEFT);
                $nombreArchivo = $legajo . '.jpg';
                $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
                
                // Guardar en storage/app/public/fotos-empleados
                $path = $this->nuevaFoto->storeAs('fotos-empleados', $nombreArchivo, 'public');
                
                // Eliminar el marcador si existe
                if (Storage::disk('public')->exists($marcadorEliminada)) {
                    Storage::disk('public')->delete($marcadorEliminada);
                }
                
                $cambiosRealizados = true;
                $mensajesCambios[] = 'foto actualizada';
                $this->nuevaFoto = null;
            }

            // Recargar la foto actual después de todos los cambios
            $this->cargarFotoActual();

            // Mostrar mensaje apropiado
            if ($cambiosRealizados) {
                if (count($mensajesCambios) > 1) {
                    session()->flash('success', 'Se actualizaron: ' . implode(', ', $mensajesCambios) . '.');
                } else {
                    session()->flash('success', 'Se actualizó: ' . $mensajesCambios[0] . '.');
                }
            } else {
                session()->flash('error', 'No se realizaron cambios. Verifica que los datos sean diferentes.');
            }
            
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error al actualizar perfil: ' . $e->getMessage(), [
                'legajo' => Auth::user()->LEGAJO,
                'domicilio' => $this->domicilio,
                'telefono' => $this->telefono,
                'mail' => $this->mail,
                'tiene_nueva_foto' => $this->nuevaFoto !== null,
                'eliminar_foto_flag' => $this->eliminarFotoFlag,
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Hubo un error al actualizar tus datos. Por favor, intenta nuevamente.');
        }
    }
    
    public function cancel()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.perfil')->layout('components.layouts.autogestion');
    }
}