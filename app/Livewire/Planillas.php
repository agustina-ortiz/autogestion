<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Familia;
use App\Models\Planilla;

class Planillas extends Component
{
    // NO usar WithFileUploads
    
    public $selectedDni;
    public $selectedNombre;
    public $planillaActual;
    public $anioActual;
    public $hijos = [];
    
    public $modalVerPlanilla = false;
    public $rutaPlanillaVer = null;
    public $extensionPlanillaVer = null;

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
            $hijosQuery = Familia::obtenerHijosParaPlanillas($legajo);
            
            $this->hijos = $hijosQuery->map(function ($hijo) {
                $estado = $hijo->getEstadoPlanilla($this->planillaActual, $this->anioActual);
                
                // Obtener información adicional de la planilla si existe
                $planilla = Planilla::where('legajo', $this->legajo ?? Auth::user()->LEGAJO)
                    ->where('dni', $hijo->DNI)
                    ->where('planilla', $this->planillaActual)
                    ->where('anio', $this->anioActual)
                    ->first();
                
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
                    'confirmada' => $planilla ? $planilla->confirmada : null,
                    'observaciones' => $planilla ? $planilla->observa : null,
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
                'legajo' => $legajo
            ]);
            session()->flash('error', 'Error al cargar datos: ' . $e->getMessage());
        }
    }

    private function obtenerPlanillaActual()
    {
        $mes = now()->month;
        
        if (in_array($mes, [2, 3, 4, 5, 6])) {
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
            $hijo = Familia::porLegajo($legajo)
                ->soloHijos()
                ->where('DNI', $dni)
                ->first();

            if (!$hijo) {
                session()->flash('error', 'No se encontró el registro del hijo con DNI: ' . $dni);
                return;
            }

            $fpdfPath = base_path('vendor/setasign/fpdf/fpdf.php');
            
            if (!file_exists($fpdfPath)) {
                session()->flash('error', 'FPDF no está instalado.');
                return;
            }
            
            require_once $fpdfPath;

            $pdf = new \FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetMargins(20, 20, 20);

            $rutaLogo = public_path('img/encabezado.png');
            if (file_exists($rutaLogo)) {
                $pdf->Image($rutaLogo, 20, 15, 170);
                $pdf->Ln(40);
            }

            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, $this->convertirTexto('Planilla de Escolaridad'), 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, $this->convertirTexto('Año Lectivo ' . $this->anioActual), 0, 1, 'C');
            $pdf->Ln(8);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(70, 7, $this->convertirTexto('Apellido y Nombre del Padre/Madre:'), 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, $this->convertirTexto(Auth::user()->NOMBRE ?? Auth::user()->name), 0, 1);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(70, 7, $this->convertirTexto('N° de Legajo:'), 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, $legajo, 0, 1);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(70, 7, 'D.N.I.:', 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, Auth::user()->DNI ?? '-', 0, 1);
            
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->MultiCell(0, 6, $this->convertirTexto("ASIGNACIONES FAMILIARES\nCERTIFICADO LEY 24714"), 0, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', 11);
            $texto = "CERTIFICO QUE: " . strtoupper($hijo->NOMBRE) . " ha sido registrado/a en este Establecimiento para cursar como alumno regular, durante el ciclo lectivo " . $this->anioActual . ".";
            $pdf->MultiCell(0, 6, $this->convertirTexto($texto), 0, 'J');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(60, 7, $this->convertirTexto('Nombre del Establecimiento:'), 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, '_________________________________________________', 0, 1);

            $pdf->Ln(3);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(10, 7, '', 0, 0);
            $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1, 4, 4);
            $pdf->Cell(6, 7, '', 0, 0);
            $pdf->Cell(0, 7, $this->convertirTexto('Establecimiento del ESTADO'), 0, 1);

            $pdf->Ln(1);

            $pdf->Cell(10, 7, '', 0, 0);
            $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1, 4, 4);
            $pdf->Cell(6, 7, '', 0, 0);
            $pdf->Cell(0, 7, $this->convertirTexto('Establecimiento incorporado o adscripto por Resolucion N°: __________'), 0, 1);

            $pdf->Ln(8);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 7, 'Domicilio:', 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, '_____________________________________________________________', 0, 1);

            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 7, 'Localidad:', 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, '_____________________________________________________________', 0, 1);
            
            $pdf->Ln(15);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, '...................................................', 0, 1, 'R');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, $this->convertirTexto('Firma y Sello del Establecimiento'), 0, 1, 'R');
            
            $pdf->Ln(5);

            $fechaLimite = $this->planillaActual == 1 ? '30 de marzo' : '30 de diciembre';
            $pdf->SetFont('Arial', 'I', 10);
            $textoNota = "Este certificado debe presentarse en la oficina de Recursos Humanos antes del dia " . $fechaLimite . ", caso contrario, de acuerdo a la Ley 24714, debera cancelarse el pago del adicional por escolaridad.";
            $pdf->MultiCell(0, 5, $this->convertirTexto($textoNota), 0, 'J');

            $nombrePDF = 'Planilla_Escolaridad_' . $hijo->NOMBRE . '_' . $this->planillaActual . '_' . $this->anioActual . '.pdf';
            $nombrePDF = str_replace(' ', '_', $nombrePDF);

            $tempPath = sys_get_temp_dir() . '/' . uniqid('planilla_') . '.pdf';
            $pdf->Output('F', $tempPath);

            $pdfContent = file_get_contents($tempPath);
            unlink($tempPath);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $nombrePDF . '"');

        } catch (\Exception $e) {
            \Log::error('Error al generar planilla PDF', [
                'error' => $e->getMessage(),
                'dni' => $dni,
                'legajo' => $legajo
            ]);
            session()->flash('error', 'Error al generar la planilla: ' . $e->getMessage());
        }
    }

    private function convertirTexto($texto)
    {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }

    public function seleccionarHijo($dni, $nombre)
    {
        $this->selectedDni = $dni;
        $this->selectedNombre = $nombre;
    }

    public function cerrarModal()
    {
        $this->selectedDni = null;
        $this->selectedNombre = null;
    }

    public function verPlanilla($dni)
    {
        try {
            // Buscar archivos en public/fotos-licencias/fotos-empleados/planillas
            $directorioPublico = public_path('fotos-licencias/fotos-empleados/planillas');
            
            if (!file_exists($directorioPublico)) {
                session()->flash('error', 'Directorio de planillas no encontrado.');
                \Log::warning('Directorio no existe', ['path' => $directorioPublico]);
                return;
            }
            
            $dniPadded = str_pad($dni, 8, '0', STR_PAD_LEFT);
            
            // Buscar archivos que coincidan con el patrón: {dni}{planilla}-{año}.{ext}
            $patronJpg = $dniPadded . $this->planillaActual . '-' . $this->anioActual . '.jpg';
            $patronPdf = $dniPadded . $this->planillaActual . '-' . $this->anioActual . '.pdf';
            
            $archivoEncontrado = false;
            
            $numAleatorio = rand(1, 99);

            // Verificar JPG primero
            if (file_exists($directorioPublico . '/' . $patronJpg)) {
                $this->rutaPlanillaVer = asset('fotos-licencias/fotos-empleados/planillas/' . $patronJpg . "?" . $numAleatorio);
                $this->extensionPlanillaVer = 'jpg';
                $this->modalVerPlanilla = true;
                $archivoEncontrado = true;
                
                \Log::info('Planilla JPG encontrada', [
                    'dni' => $dni,
                    'archivo' => $patronJpg
                ]);
            }
            // Verificar PDF
            elseif (file_exists($directorioPublico . '/' . $patronPdf)) {
                $this->rutaPlanillaVer = asset('fotos-licencias/fotos-empleados/planillas/' . $patronPdf);
                $this->extensionPlanillaVer = 'pdf';
                $this->modalVerPlanilla = true;
                $archivoEncontrado = true;
                
                \Log::info('Planilla PDF encontrada', [
                    'dni' => $dni,
                    'archivo' => $patronPdf
                ]);
            }
            
            if (!$archivoEncontrado) {
                session()->flash('error', 'Planilla no encontrada.');
                
                \Log::warning('Planilla no encontrada', [
                    'dni' => $dni,
                    'planilla' => $this->planillaActual,
                    'anio' => $this->anioActual,
                    'buscando_jpg' => $patronJpg,
                    'buscando_pdf' => $patronPdf
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error al ver planilla', [
                'error' => $e->getMessage(),
                'dni' => $dni
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

    public function render()
    {
        return view('livewire.planillas')->layout('components.layouts.autogestion');
    }
}