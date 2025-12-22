<?php

namespace App\Services;

use FPDF;

class ReciboPDF extends FPDF
{
    private $recibo;
    private $persona;
    private $conceptos;

    public function __construct($recibo, $persona, $conceptos)
    {
        parent::__construct('P', 'mm', 'A4');
        
        // Normalizar las claves a mayúsculas
        $this->recibo = $this->normalizarClaves($recibo);
        $this->persona = $this->normalizarClaves($persona);
        $this->conceptos = array_map(function($concepto) {
            return $this->normalizarClaves($concepto);
        }, $conceptos);
    }

    /**
     * Normaliza todas las claves de un array a MAYÚSCULAS
     */
    private function normalizarClaves($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        
        $normalized = [];
        foreach ($array as $key => $value) {
            $normalized[strtoupper($key)] = $value;
        }
        return $normalized;
    }

    /**
     * Obtiene un valor del array con fallback si no existe
     */
    private function getValue($array, $key, $default = '')
    {
        return isset($array[strtoupper($key)]) ? $array[strtoupper($key)] : $default;
    }

    // Encabezado del PDF
    function Header()
    {
        // Logo (esquina superior izquierda)
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 8, 60); 
        }

        // Mover el cursor hacia abajo para no pisar el logo
        $this->Ln(10);

        // Título
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(119, 191, 67); // Color #77BF43
        $this->Cell(0, 10, utf8_decode('MUNICIPALIDAD DE MERCEDES'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, utf8_decode('RECIBO DE SUELDO'), 0, 1, 'C');
        
        // Línea separadora
        $this->SetDrawColor(119, 191, 67);
        $this->SetLineWidth(0.8);
        $this->Line(10, $this->GetY() + 2, 200, $this->GetY() + 2);
        $this->Ln(5);

    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 5, utf8_decode('Sistema de Autogestión de Recursos Humanos - Municipalidad de Mercedes'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Documento generado el ' . date('d/m/Y H:i')), 0, 0, 'C');
    }

    // Función para crear encabezados de sección
    private function seccionHeader($titulo)
    {
        $this->SetFillColor(119, 191, 67); // Color #77BF43
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, utf8_decode($titulo), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    // Función auxiliar para agregar una fila de información
    private function agregarFila($label, $value, $yPos = null)
    {
        if ($yPos !== null) {
            $this->SetY($yPos);
        }
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(50, 5, utf8_decode($label . ':'), 0, 0, 'L');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->Cell(0, 5, utf8_decode($value), 0, 1, 'L');
    }

    // Generar el contenido del PDF
    public function generarContenido()
    {
        $this->SetAutoPageBreak(true, 27);
        $this->AddPage();

        // SECCIÓN: DATOS PERSONALES Y LABORALES (DOS COLUMNAS)
        $yInicial = $this->GetY();
        
        // Columna izquierda - DATOS PERSONALES
        $this->SetX(10);
        $this->seccionHeader('DATOS PERSONALES');

        // IMPORTANTE: En MySQL el campo se llama NOMBRE (completo), no APELLIDO y NOMBRES
        $nombreCompleto = $this->getValue($this->persona, 'NOMBRE', 'N/A');
        $legajo = $this->getValue($this->recibo, 'LEGAJO', 'N/A');
        $nroDoc = $this->getValue($this->persona, 'DNI', 'N/A'); // En MySQL es DNI
        $nroCuit = $this->getValue($this->persona, 'CUIL', 'N/A'); // En MySQL es CUIL

        $this->agregarFila('Nombre', $nombreCompleto);
        $this->agregarFila('Legajo', $legajo);
        $this->agregarFila('DNI', $nroDoc);
        $this->agregarFila('CUIL', $nroCuit);
                
        $yFinalIzq = $this->GetY();

        // Columna derecha - DATOS LABORALES
        $this->SetY($yInicial);
        $this->SetX(110);

        $xDerecha = 110;
        $this->SetX($xDerecha);
        $this->SetFillColor(119, 191, 67);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(90, 7, utf8_decode('DATOS LABORALES'), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);

        $yActual = $this->GetY() + 2;

        // Obtener datos (vienen de Oracle ahora)
        $desCategoria = $this->getValue($this->persona, 'DES_CATEGORIA', 'N/A');
        $codCategoria = $this->getValue($this->persona, 'COD_CATEGORIA', 'N/A');
        $jurisdiccion = $this->getValue($this->persona, 'JURISDICCION', 'N/A');
        $desTipoPlanta = $this->getValue($this->persona, 'DES_TIPO_PLANTA', 'N/A');
        $fechAntig = $this->getValue($this->persona, 'FECH_ANTIG', '');

        // Si no vienen de Oracle, usar datos de MySQL
        if ($desCategoria === 'N/A') {
            $categoriaMySQL = $this->getValue($this->persona, 'CATEGORIA', '');
            $desCategoria = !empty($categoriaMySQL) ? "CATEGORÍA {$categoriaMySQL}" : 'N/A';
        }

        $fechaIngreso = 'N/A';
        if (!empty($fechAntig)) {
            $timestamp = strtotime($fechAntig);
            if ($timestamp !== false) {
                $fechaIngreso = date('d/m/Y', $timestamp);
            }
        } else {
            // Fallback a FECING de MySQL
            $fecingMySQL = $this->getValue($this->persona, 'FECING', '');
            if (!empty($fecingMySQL)) {
                $timestamp = strtotime($fecingMySQL);
                if ($timestamp !== false) {
                    $fechaIngreso = date('d/m/Y', $timestamp);
                }
            }
        }

        // Cargo (debe mostrar como "CATEGORÍA {número}")
        $this->SetY($yActual);
        $this->SetX($xDerecha);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(30, 5, utf8_decode('Cargo:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->MultiCell(60, 5, utf8_decode($desCategoria), 0, 'L');

        // F. Ingreso
        $this->SetX($xDerecha);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(30, 5, utf8_decode('F. Ingreso:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->Cell(60, 5, $fechaIngreso, 0, 1, 'L');

        // Categoría
        $this->SetX($xDerecha);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(30, 5, utf8_decode('Categoría:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->Cell(60, 5, utf8_decode($codCategoria), 0, 1, 'L');

        // Planta
        $this->SetX($xDerecha);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(30, 5, utf8_decode('Planta:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->MultiCell(60, 5, utf8_decode($desTipoPlanta), 0, 'L');

        // Jurisdicción (en lugar de Departamento)
        $this->SetX($xDerecha);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85, 85, 85);
        $this->Cell(30, 5, utf8_decode('Jurisdicción:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(51, 51, 51);
        $this->MultiCell(60, 5, utf8_decode($jurisdiccion), 0, 'L');
        
        $yFinalDer = $this->GetY();
        
        // Continuar después de la sección más larga
        $this->SetY(max($yFinalIzq, $yFinalDer) + 5);

        // TABLA: INFORMACIÓN DEL RECIBO
        $nroRecibo = $this->getValue($this->recibo, 'NRO_RECIBO', 'N/A');
        $mes = $this->getValue($this->recibo, 'MES', 'N/A');
        $anio = $this->getValue($this->recibo, 'ANIO', 'N/A');
        $tipoLiq = $this->getValue($this->recibo, 'TIPO_LIQ', 'N/A');

        // Cabecera
        $this->SetFillColor(119, 191, 67);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(63.33, 7, utf8_decode('Recibo N°'), 1, 0, 'C', true);
        $this->Cell(63.33, 7, utf8_decode('Período'), 1, 0, 'C', true);
        $this->Cell(63.34, 7, utf8_decode('Tipo'), 1, 1, 'C', true);

        // Valores
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(63.33, 7, $nroRecibo, 1, 0, 'C');
        $this->Cell(63.33, 7, "{$mes}/{$anio}", 1, 0, 'C');
        $this->Cell(63.34, 7, utf8_decode($tipoLiq), 1, 1, 'C');

        $this->Ln(10);

        // SECCIÓN: DETALLE DE CONCEPTOS
        $this->seccionHeader('DETALLE DE CONCEPTOS');
        
        // Encabezado de la tabla
        $this->SetFillColor(119, 191, 67);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        
        $this->Cell(15, 6, 'Cant.', 1, 0, 'C', true);
        $this->Cell(20, 6, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(90, 6, 'Concepto', 1, 0, 'C', true);
        $this->Cell(32.5, 6, 'Haberes', 1, 0, 'C', true);
        $this->Cell(32.5, 6, 'Descuento', 1, 1, 'C', true);
        
        // Contenido de la tabla
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 8);
        
        $fill = false;
        foreach ($this->conceptos as $concepto) {
            // Verificar si necesitamos una nueva página
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Re-dibujar el encabezado de la tabla
                $this->SetFillColor(119, 191, 67);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('Arial', 'B', 8);
                
                $this->Cell(15, 6, 'Cant.', 1, 0, 'C', true);
                $this->Cell(20, 6, utf8_decode('Código'), 1, 0, 'C', true);
                $this->Cell(90, 6, 'Concepto', 1, 0, 'C', true);
                $this->Cell(32.5, 6, 'Haberes', 1, 0, 'C', true);
                $this->Cell(32.5, 6, 'Descuento', 1, 1, 'C', true);
                
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Arial', '', 8);
            }
            
            $this->SetFillColor(249, 249, 249);
            
            $cantidad = $this->getValue($concepto, 'CANTIDAD', '-');
            $codigo = $this->getValue($concepto, 'CONCEPTO', '-');
            $descripcion = $this->getValue($concepto, 'DESC_CONCEPTO', '-');
            $monto = $this->getValue($concepto, 'MONTO', 0);
            
            $haber = $monto > 0 ? '$' . number_format($monto, 2, ',', '.') : '-';
            $descuento = $monto < 0 ? '$' . number_format($monto, 2, ',', '.') : '-';
            
            $this->Cell(15, 5, $cantidad, 1, 0, 'C', $fill);
            $this->Cell(20, 5, $codigo, 1, 0, 'C', $fill);
            $this->Cell(90, 5, utf8_decode($descripcion), 1, 0, 'L', $fill);
            $this->Cell(32.5, 5, $haber, 1, 0, 'R', $fill);
            $this->Cell(32.5, 5, $descuento, 1, 1, 'R', $fill);
            
            $fill = !$fill;
        }
        
        $this->Ln(10);

        // SECCIÓN: RESUMEN DE LIQUIDACIÓN
        $this->seccionHeader('RESUMEN DE LIQUIDACIÓN');
        
        // TABLA: RESUMEN DE LIQUIDACIÓN
        $remunCAp = $this->getValue($this->recibo, 'REMUN_C_AP', 0);
        $remunSAp = $this->getValue($this->recibo, 'REMUN_S_AP', 0);
        $retenciones = $this->getValue($this->recibo, 'RETENCIONES', 0);

        // Ancho total útil: 190mm
        $colW = 190 / 3;

        // Cabecera
        $this->SetFillColor(119, 191, 67);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);

        $this->Cell($colW, 8, utf8_decode('Remuneración con Aporte'), 1, 0, 'C', true);
        $this->Cell($colW, 8, utf8_decode('Remuneración sin Aporte'), 1, 0, 'C', true);
        $this->Cell($colW, 8, utf8_decode('Retenciones'), 1, 1, 'C', true);

        // Valores
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 10);

        $this->Cell($colW, 8, '$' . number_format($remunCAp, 2, ',', '.'), 1, 0, 'R');
        $this->Cell($colW, 8, '$' . number_format($remunSAp, 2, ',', '.'), 1, 0, 'R');

        $this->SetTextColor(220, 53, 69);
        $this->Cell($colW, 8, '$' . number_format($retenciones, 2, ',', '.'), 1, 1, 'R');

        // Reset colores
        $this->SetTextColor(0, 0, 0);
        $this->Ln(8);

        // LÍQUIDO A COBRAR destacado
        $liquido = $this->getValue($this->recibo, 'LIQUIDO', 0);
        $this->SetFillColor(119, 191, 67);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        
        $yLiquido = $this->GetY();

        // Verificar espacio antes de dibujar el líquido
        if ($yLiquido > 260) {
            $this->AddPage();
            $yLiquido = $this->GetY();
        }

        $this->Rect(10, $yLiquido, 190, 12, 'F');
        $this->SetY($yLiquido + 3);

        $this->Cell(140, 6, utf8_decode('LÍQUIDO A COBRAR:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(50, 6, '$' . number_format($liquido, 2, ',', '.'), 0, 1, 'R');
    }
}