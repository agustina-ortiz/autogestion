<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;

class Planillas extends Component
{
    use WithFileUploads;

    public $foto;
    public $selectedDni;
    public $selectedNombre;
    public $planillaActual;
    public $anioActual;
    public $hijos = [];

    public $mostrarModalImpresion = false;
    public $contenidoImpresion = null;
    
    // Nuevas propiedades para ver planilla
    public $modalVerPlanilla = false;
    public $rutaPlanillaVer = null;
    
    protected $rules = [
        'foto' => 'required|image|max:5120',
    ];

    protected $messages = [
        'foto.required' => 'Debe seleccionar una imagen',
        'foto.image' => 'El archivo debe ser una imagen',
        'foto.max' => 'La imagen no debe superar 5MB',
    ];

    public function mount()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $legajo = Auth::user()->LEGAJO;
        
        if (!$legajo) {
            \Log::error('No se encontró legajo en sesión');
            session()->flash('error', 'No se pudo obtener el legajo del usuario');
            return;
        }
        
        $this->planillaActual = $this->obtenerPlanillaActual();
        $this->anioActual = $this->obtenerAnioActual();
        
        if ($this->planillaActual == 0) {
            return;
        }

        try {
            $campoPlanilla = 'PLANILLA' . $this->planillaActual;
            
            $this->hijos = DB::connection('mysql1')
                ->table('in_familia')
                ->select(
                    'NOMBRE as nombre',
                    'DNI as dni',
                    'FECHA_NAC as fecha_nac',
                    'TIPOFAMI as tipofami',
                    'TIPOESCO as tipoesco',
                    'CURSO as curso',
                    'ESCUELA as escuela',
                    DB::raw("CASE 
                        WHEN {$campoPlanilla} IS NOT NULL 
                        AND {$campoPlanilla} != '' 
                        AND {$campoPlanilla} != '\\N'
                        THEN 1 
                        ELSE 0 
                    END as tiene_planilla"),
                    DB::raw("{$campoPlanilla} as archivo_planilla")
                )
                ->where('LEGAJO', '=', $legajo)
                ->where('TIPOFAMI', '=', 2)
                ->orderBy('FECHA_NAC', 'asc')
                ->get();
            
            \Log::info('Hijos cargados', [
                'legajo' => $legajo,
                'planilla' => $this->planillaActual,
                'count' => count($this->hijos),
                'hijos' => $this->hijos->toArray()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en cargarDatos', [
                'error' => $e->getMessage(),
                'legajo' => $legajo,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al cargar datos: ' . $e->getMessage());
        }
    }

    private function obtenerPlanillaActual()
    {
        $mes = now()->month;
        
        if (in_array($mes, [2, 3, 4])) {
            return 1;
        } elseif (in_array($mes, [11, 12, 1])) {
            return 2;
        }
        
        return 0;
    }

    private function obtenerAnioActual()
    {
        $mes = now()->month;
        $anio = now()->year;
        
        if ($mes == 1) {
            return $anio - 1;
        }
        
        return $anio;
    }

    public function descargarPlanilla($dni, $nombre)
    {
        $legajo = Auth::user()->LEGAJO;

        try {
            $hijo = DB::connection('mysql1')
                ->table('in_familia')
                ->where('LEGAJO', '=', $legajo)
                ->where('DNI', '=', $dni)
                ->where('TIPOFAMI', '=', 2)
                ->first();

            if (!$hijo) {
                session()->flash('error', 'No se encontró el registro del hijo con DNI: ' . $dni);
                return;
            }

            // Armar los datos para la planilla
            $this->contenidoImpresion = [
                'planilla' => $this->planillaActual,
                'anio' => $this->anioActual,
                'nombre' => $hijo->NOMBRE,
                'dni' => $hijo->DNI,
                'legajo' => $legajo,
                'nombrePadre' => Auth::user()->NOMBRE ?? Auth::user()->name,
            ];

            // Mostrar modal de impresión
            $this->mostrarModalImpresion = true;

            // Esperar a que el DOM se actualice y abrir diálogo de impresión
            $this->dispatch('abrirModalImpresion');

        } catch (\Exception $e) {
            \Log::error('Error al generar planilla', [
                'error' => $e->getMessage(),
                'dni' => $dni,
                'legajo' => $legajo
            ]);
            session()->flash('error', 'Error al generar la planilla: ' . $e->getMessage());
        }
    }

    public function seleccionarHijo($dni, $nombre)
    {
        $this->selectedDni = $dni;
        $this->selectedNombre = $nombre;
        $this->foto = null;
    }

    public function subirPlanilla()
    {
        $this->validate();

        $legajo = Auth::user()->LEGAJO;
        
        try {
            DB::connection('mysql1')->beginTransaction();

            $nombreArchivo = $this->zerofill($this->selectedDni, 8) .
                            $this->planillaActual . '-' .
                            $this->anioActual . '.jpg';

            $rutaRelativa = 'fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo;
            $rutaCompleta = public_path($rutaRelativa);

            // Crear directorio si no existe
            $directorio = dirname($rutaCompleta);
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Guardar imagen
            $image = Image::read($this->foto->getRealPath());
            $image->save($rutaCompleta);

            \Log::info('Imagen guardada', [
                'ruta' => $rutaCompleta,
                'existe' => file_exists($rutaCompleta),
                'permisos' => substr(sprintf('%o', fileperms($rutaCompleta)), -4)
            ]);

            // 1️⃣ Actualizar en in_familia
            $campoActualizar = 'PLANILLA' . $this->planillaActual;
            DB::connection('mysql1')
                ->table('in_familia')
                ->where('LEGAJO', '=', $legajo)
                ->where('DNI', '=', $this->selectedDni)
                ->update([
                    $campoActualizar => 'S'
                ]);

            // 2️⃣ Insertar o reemplazar en in_planillas
            DB::connection('mysql1')->table('in_planillas')
                ->where('legajo', '=', $legajo)
                ->where('anio', '=', $this->anioActual)
                ->where('planilla', '=', $this->planillaActual)
                ->where('dni', '=', $this->selectedDni)
                ->delete();

            DB::connection('mysql1')->table('in_planillas')->insert([
                'legajo'      => $legajo,
                'anio'        => $this->anioActual,
                'planilla'    => $this->planillaActual,
                'dni'         => $this->selectedDni,
                'fecha'       => DB::raw('CURDATE()'),
                'confirmada'  => false,
            ]);

            DB::connection('mysql1')->commit();

            // Limpiar estado y refrescar vista
            $this->reset(['foto', 'selectedDni', 'selectedNombre']);
            $this->cargarDatos();

            session()->flash('mensaje', 'Planilla subida exitosamente');

        } catch (\Exception $e) {
            DB::connection('mysql1')->rollBack();

            \Log::error('Error al subir planilla', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error al subir la planilla: ' . $e->getMessage());
        }
    }

    public function verPlanilla($dni)
    {
        $legajo = Auth::user()->LEGAJO;
        
        try {
            $nombreArchivo = $this->zerofill($dni, 8) . 
                           $this->planillaActual . '-' . 
                           $this->anioActual . '.jpg';
            
            $rutaRelativa = 'fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo;
            $rutaCompleta = public_path($rutaRelativa);
            
            \Log::info('Intentando ver planilla', [
                'dni' => $dni,
                'nombre_archivo' => $nombreArchivo,
                'ruta_completa' => $rutaCompleta,
                'existe' => file_exists($rutaCompleta)
            ]);
            
            if (file_exists($rutaCompleta)) {
                // Guardamos la ruta relativa para mostrar en el modal
                $this->rutaPlanillaVer = asset($rutaRelativa);
                $this->modalVerPlanilla = true;
            } else {
                session()->flash('error', 'Planilla no encontrada en el servidor. Verifica que se haya subido correctamente.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Error al ver planilla', [
                'error' => $e->getMessage(),
                'dni' => $dni,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al buscar la planilla: ' . $e->getMessage());
        }
    }

    public function cerrarModalVer()
    {
        $this->modalVerPlanilla = false;
        $this->rutaPlanillaVer = null;
    }

    private function zerofill($num, $zerofill = 8)
    {
        return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.planillas')->layout('components.layouts.autogestion');
    }
}