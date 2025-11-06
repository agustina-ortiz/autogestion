<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Familia;
use App\Models\Planilla;

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
    
    // Propiedades para ver planilla
    public $modalVerPlanilla = false;
    public $rutaPlanillaVer = null;
    public $extensionPlanillaVer = null;
    
    protected $rules = [
        'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ];

    protected $messages = [
        'foto.required' => 'Debe seleccionar una imagen',
        'foto.image' => 'El archivo debe ser una imagen',
        'foto.mimes' => 'Solo se permiten imágenes JPG o PNG',
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
            // Obtener hijos usando el modelo Familia
            $hijosQuery = Familia::obtenerHijosParaPlanillas($legajo);
            
            $this->hijos = $hijosQuery->map(function ($hijo) {
                // Obtener el estado de la planilla
                $estado = $hijo->getEstadoPlanilla($this->planillaActual, $this->anioActual);
                
                return (object)[
                    'nombre' => $hijo->NOMBRE,
                    'dni' => $hijo->DNI,
                    'fecha_nac' => $hijo->FECHA_NAC,
                    'tipofami' => $hijo->TIPOFAMI,
                    'tipoesco' => $hijo->TIPOESCO,
                    'curso' => $hijo->CURSO,
                    'escuela' => $hijo->ESCUELA,
                    'archivo_planilla' => $hijo->{'PLANILLA' . $this->planillaActual},
                    'estado_planilla' => $estado['estado'],
                    'tiene_planilla' => $estado['tiene_planilla'],
                ];
            })->toArray();
            
            \Log::info('Hijos cargados con modelos', [
                'legajo' => $legajo,
                'planilla' => $this->planillaActual,
                'count' => count($this->hijos)
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

    /**
     * Determina qué planilla corresponde según el mes actual
     */
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

    /**
     * Obtiene el año de la planilla según el mes actual
     */
    private function obtenerAnioActual()
    {
        $mes = now()->month;
        $anio = now()->year;
        
        // Si estamos en enero, la planilla es del año anterior
        if ($mes == 1) {
            return $anio - 1;
        }
        
        return $anio;
    }

    /**
     * Genera una planilla en blanco para imprimir
     */
    public function descargarPlanilla($dni, $nombre)
    {
        $legajo = Auth::user()->LEGAJO;

        try {
            // Buscar el hijo usando el modelo Familia
            $hijo = Familia::porLegajo($legajo)
                ->soloHijos()
                ->where('DNI', $dni)
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

            // Emitir evento para abrir el diálogo de impresión
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

    /**
     * Selecciona un hijo para subir su planilla
     */
    public function seleccionarHijo($dni, $nombre)
    {
        $this->selectedDni = $dni;
        $this->selectedNombre = $nombre;
        $this->foto = null;
    }

    /**
     * Sube la planilla escaneada
     */
    public function subirPlanilla()
    {
        $this->validate();

        $legajo = Auth::user()->LEGAJO;
        
        try {
            DB::connection('mysql')->beginTransaction();

            // Generar nombre del archivo (siempre JPG)
            $nombreArchivo = $this->zerofill($this->selectedDni, 8) .
                            $this->planillaActual . '-' .
                            $this->anioActual . '.jpg';

            // Definir rutas
            $directorioRelativo = 'fotos-licencias' . DIRECTORY_SEPARATOR . 
                                 'fotos-empleados' . DIRECTORY_SEPARATOR . 
                                 'planillas';
            
            $rutaCompleta = public_path($directorioRelativo . DIRECTORY_SEPARATOR . $nombreArchivo);
            $directorio = public_path($directorioRelativo);

            // Crear directorio si no existe
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Procesar y guardar la imagen siempre como JPG
            $image = Image::read($this->foto->getRealPath());
            $image->toJpeg(90); // Convertir a JPG con calidad 90%
            $image->save($rutaCompleta);

            \Log::info('Imagen guardada', [
                'nombre' => $nombreArchivo,
                'ruta' => $rutaCompleta,
                'existe' => file_exists($rutaCompleta),
                'tamaño' => file_exists($rutaCompleta) ? filesize($rutaCompleta) : 0
            ]);

            // Verificar que el archivo realmente se guardó
            if (!file_exists($rutaCompleta)) {
                throw new \Exception('La imagen no se guardó correctamente en: ' . $rutaCompleta);
            }

            // Eliminar registro previo si existe (usando modelo Planilla)
            Planilla::porLegajo($legajo)
                ->porPeriodo($this->anioActual, $this->planillaActual)
                ->porDni($this->selectedDni)
                ->delete();

            // Insertar nuevo registro usando el modelo Planilla
            Planilla::create([
                'legajo'      => $legajo,
                'anio'        => $this->anioActual,
                'planilla'    => $this->planillaActual,
                'dni'         => $this->selectedDni,
                'fecha'       => now()->toDateString(),
                'confirmada'  => false,
            ]);

            DB::connection('mysql')->commit();

            // Limpiar estado y refrescar vista
            $this->reset(['foto', 'selectedDni', 'selectedNombre']);
            $this->cargarDatos();

            session()->flash('mensaje', 'Planilla subida exitosamente');

        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();

            \Log::error('Error al subir planilla', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error al subir la planilla: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la planilla subida en un modal
     */
    public function verPlanilla($dni)
    {
        try {
            $nombreArchivo = $this->zerofill($dni, 8) . 
                           $this->planillaActual . '-' . 
                           $this->anioActual . '.jpg';
            
            $rutaCompleta = public_path('fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo);
            
            \Log::info('Intentando ver planilla', [
                'dni' => $dni,
                'nombre_archivo' => $nombreArchivo,
                'ruta_completa' => $rutaCompleta,
                'existe' => file_exists($rutaCompleta)
            ]);
            
            if (file_exists($rutaCompleta)) {
                // Guardamos la ruta relativa para mostrar en el modal
                $rutaRelativa = 'fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo;
                $this->rutaPlanillaVer = asset($rutaRelativa);
                $this->extensionPlanillaVer = 'jpg';
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

    /**
     * Cierra el modal de visualización de planilla
     */
    public function cerrarModalVer()
    {
        $this->modalVerPlanilla = false;
        $this->rutaPlanillaVer = null;
        $this->extensionPlanillaVer = null;
    }

    /**
     * Rellena con ceros a la izquierda
     */
    private function zerofill($num, $zerofill = 8)
    {
        return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.planillas')->layout('components.layouts.autogestion');
    }
}