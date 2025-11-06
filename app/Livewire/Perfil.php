<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Perfil extends Component
{
    public $nombre = '';
    public $domicilio = '';
    public $telefono = '';
    public $mail = '';
    
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
            } else {
                session()->flash('error', 'No se encontraron datos del empleado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los datos: ' . $e->getMessage());
        }
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
            // Actualizar en la base de datos
            $affected = DB::table('in_maestro')
                ->where('LEGAJO', Auth::user()->LEGAJO)
                ->update([
                    'DOMICILIO' => $this->domicilio ?: null,
                    'TELEFONO' => $this->telefono ?: null,
                    'MAIL' => $this->mail ?: null,
                ]);
            
            // Verificar si se actualizó algún registro
            if ($affected > 0) {
                session()->flash('success', 'Tus datos han sido actualizados correctamente.');
            } else {
                session()->flash('error', 'No se pudo actualizar la información. Verifica que los datos sean diferentes.');
            }
            
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error al actualizar perfil: ' . $e->getMessage(), [
                'legajo' => Auth::user()->LEGAJO,
                'domicilio' => $this->domicilio,
                'telefono' => $this->telefono,
                'mail' => $this->mail
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