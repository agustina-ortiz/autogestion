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
                    DB::raw("{$campoPlanilla} as archivo_planilla")
                )
                ->where('LEGAJO', '=', $legajo)
                ->where('TIPOFAMI', '=', 2)
                ->orderBy('FECHA_NAC', 'asc')
                ->get();
            
            // Verificar si existe el archivo físico para cada hijo
            foreach ($this->hijos as $hijo) {
                $nombreArchivo = $this->zerofill($hijo->dni, 8) . 
                               $this->planillaActual . '-' . 
                               $this->anioActual . '.jpg';
                
                $rutaCompleta = public_path('fotos-licencias/fotos-empleados/planillas/' . $nombreArchivo);
                $archivoExiste = file_exists($rutaCompleta);
                
                // Verificar si existe en in_planillas
                $existeEnPlanillas = DB::connection('mysql1')
                    ->table('in_planillas')
                    ->where('legajo', '=', $legajo)
                    ->where('dni', '=', $hijo->dni)
                    ->where('planilla', '=', $this->planillaActual)
                    ->where('anio', '=', $this->anioActual)
                    ->exists();
                
                // Determinar el estado de la planilla
                $campoBD = $hijo->archivo_planilla; // Valor del campo PLANILLA1 o PLANILLA2
                
                if ($archivoExiste && $existeEnPlanillas && $campoBD === 'S') {
                    // Archivo existe + Registro en BD + Aprobado en in_familia = SUBIDA
                    $hijo->estado_planilla = 'subida';
                    $hijo->tiene_planilla = true;
                } elseif ($archivoExiste && $existeEnPlanillas) {
                    // Archivo existe + Registro en BD pero NO aprobado = EN PROCESO
                    $hijo->estado_planilla = 'proceso';
                    $hijo->tiene_planilla = true;
                } else {
                    // No hay archivo o no está en BD = PENDIENTE
                    $hijo->estado_planilla = 'pendiente';
                    $hijo->tiene_planilla = false;
                }
                
                // Log para debugging
                \Log::info('Verificando planilla para hijo', [
                    'dni' => $hijo->dni,
                    'nombre' => $hijo->nombre,
                    'archivo_existe' => $archivoExiste,
                    'existe_en_planillas' => $existeEnPlanillas,
                    'campo_bd' => $campoBD,
                    'estado_final' => $hijo->estado_planilla
                ]);
            }
            
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

            // Siempre guardar como JPG
            $nombreArchivo = $this->zerofill($this->selectedDni, 8) .
                            $this->planillaActual . '-' .
                            $this->anioActual . '.jpg';

            // Usar DIRECTORY_SEPARATOR para compatibilidad con Windows y Linux
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
                'tamaño' => file_exists($rutaCompleta) ? filesize($rutaCompleta) : 0,
                'permisos' => file_exists($rutaCompleta) ? substr(sprintf('%o', fileperms($rutaCompleta)), -4) : 'N/A'
            ]);

            // Verificar que el archivo realmente se guardó
            if (!file_exists($rutaCompleta)) {
                throw new \Exception('La imagen no se guardó correctamente en: ' . $rutaCompleta);
            }

            // Insertar o reemplazar en in_planillas (no modificar in_familia)
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

    public function cerrarModalVer()
    {
        $this->modalVerPlanilla = false;
        $this->rutaPlanillaVer = null;
        $this->extensionPlanillaVer = null;
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