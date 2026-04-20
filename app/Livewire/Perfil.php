<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Helpers\FotoHelper;
use App\Mail\PerfilActualizado;

class Perfil extends Component
{
    public $nombre = '';
    public $domicilio = '';
    public $telefono = '';
    public $mail = '';
    public $fotoActualUrl;
    
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
                
                // Cargar foto actual usando el helper
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
        $this->fotoActualUrl = FotoHelper::obtenerUrlFoto(Auth::user()->LEGAJO);
    }

    public function eliminarFoto()
    {
        try {
            $legajo = str_pad(Auth::user()->LEGAJO, 8, '0', STR_PAD_LEFT);
            $nombreArchivo = $legajo . '.jpg';
            
            // Ruta directa al directorio público
            $directorio = public_path('fotos-licencias/fotos-empleados');
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $marcadorEliminada = $directorio . '/' . $legajo . '_eliminada.txt';
            
            // Eliminar foto si existe
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            
            // Crear archivo marcador para indicar que la foto fue eliminada
            if (!file_exists($directorio)) {
                mkdir($directorio, 0775, true);
            }
            file_put_contents($marcadorEliminada, 'eliminada');
            chmod($marcadorEliminada, 0664);

            // Actualizar la vista
            $this->cargarFotoActual();

            $this->notificarFoto('Eliminación de foto de perfil', 'El empleado eliminó su foto de perfil.');

            session()->flash('success', 'Foto eliminada correctamente.');
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar foto: ' . $e->getMessage());
            session()->flash('error', 'Hubo un error al eliminar la foto.');
        }
    }

    public function actualizarFoto()
    {
        // Este método es llamado desde JavaScript después de subir la foto exitosamente
        $this->cargarFotoActual();
    }
    
    public function rules()
    {
        return [
            'domicilio' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'mail' => 'nullable|email|max:100',
        ];
    }
    
    public function messages()
    {
        return [
            'mail.email' => 'El formato del correo electrónico no es válido.',
            'telefono.max' => 'El teléfono no puede exceder los 50 caracteres.',
            'domicilio.max' => 'El domicilio no puede exceder los 255 caracteres.',
            'mail.max' => 'El correo no puede exceder los 100 caracteres.',
        ];
    }
    
    public function save()
    {
        // Validar los datos
        $this->validate();

        try {
            $empleado = DB::table('in_maestro')
                ->where('LEGAJO', Auth::user()->LEGAJO)
                ->first();

            $datosActualizar = [];
            $cambios = [];

            if ($this->domicilio !== null && $this->domicilio !== '' && $this->domicilio !== ($empleado->DOMICILIO ?? '')) {
                $datosActualizar['DOMICILIO'] = $this->domicilio;
                $cambios['direccion'] = ['nuevo' => $this->domicilio, 'anterior' => $empleado->DOMICILIO ?? ''];
            }

            if ($this->telefono !== null && $this->telefono !== '' && $this->telefono !== ($empleado->TELEFONO ?? '')) {
                $datosActualizar['TELEFONO'] = $this->telefono;
                $cambios['telefono'] = ['nuevo' => $this->telefono, 'anterior' => $empleado->TELEFONO ?? ''];
            }

            if ($this->mail !== null && $this->mail !== '' && $this->mail !== ($empleado->MAIL ?? '')) {
                $datosActualizar['MAIL'] = $this->mail;
                $cambios['email'] = ['nuevo' => $this->mail, 'anterior' => $empleado->MAIL ?? ''];
            }

            if (empty($datosActualizar)) {
                session()->flash('error', 'No se realizaron cambios. Verifica que los datos sean diferentes.');
                return;
            }

            DB::table('in_maestro')
                ->where('LEGAJO', Auth::user()->LEGAJO)
                ->update($datosActualizar);

            $this->notificarCambios($empleado, $cambios);

            session()->flash('success', 'Datos de contacto actualizados correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar perfil: ' . $e->getMessage(), [
                'legajo' => Auth::user()->LEGAJO,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Hubo un error al actualizar tus datos. Por favor, intenta nuevamente.');
        }
    }

    private function notificarFoto(string $motivo, string $mensaje, ?string $imagen = null): void
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
                motivo: $motivo,
                mensaje: $mensaje,
                imagen: $imagen,
            ));
        } catch (\Exception $e) {
            \Log::error('Error al enviar mail de notificación de foto: ' . $e->getMessage(), [
                'legajo' => Auth::user()->LEGAJO ?? null,
            ]);
        }
    }

    private function notificarCambios($empleado, array $cambios): void
    {
        $etiquetas = [
            'direccion' => 'domicilio',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
        ];
        $campos = array_map(fn ($k) => $etiquetas[$k], array_keys($cambios));
        $mensaje = 'El empleado actualizó: ' . implode(', ', $campos) . '.';

        try {
            Mail::to(config('mail.perfil_notificacion_to'))->send(new PerfilActualizado(
                nombre: $empleado->NOMBRE ?? '',
                legajo: (string) $empleado->LEGAJO,
                motivo: 'Actualización de datos de perfil',
                mensaje: $mensaje,
                direccion: $cambios['direccion']['nuevo'] ?? null,
                direccion1: $cambios['direccion']['anterior'] ?? null,
                telefono: $cambios['telefono']['nuevo'] ?? null,
                telefono1: $cambios['telefono']['anterior'] ?? null,
                email: $cambios['email']['nuevo'] ?? null,
                email1: $cambios['email']['anterior'] ?? null,
            ));
        } catch (\Exception $e) {
            \Log::error('Error al enviar mail de notificación de perfil: ' . $e->getMessage(), [
                'legajo' => $empleado->LEGAJO ?? null,
            ]);
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