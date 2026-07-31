<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ReciboPDF;
use App\Services\ReciboVisibilidad;
use Exception;

class ReciboPDFController extends Controller
{
    private function getOracleConnection()
    {
        // Conexión Oracle con OCI - igual que en tu Livewire
        $dbstr = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.0.22)(PORT=1521))(CONNECT_DATA=(SID=MMERC10G)))";
        $conn = oci_connect('autogestion', 'autgest19', $dbstr);
        
        if (!$conn) {
            $e = oci_error();
            throw new Exception("Error de conexión Oracle: " . $e['message']);
        }
        
        return $conn;
    }

    public function generarPDF($nroRecibo, $anio, $mes, $tipoLiq)
    {
        try {
            // Convertir parámetros a los tipos correctos
            $nroRecibo = (int) $nroRecibo;
            $anio = (int) $anio;
            $mes = (int) $mes;

            Log::info('Iniciando generación de PDF', [
                'nroRecibo' => $nroRecibo,
                'anio' => $anio,
                'mes' => $mes,
                'tipoLiq' => $tipoLiq
            ]);

            // Verificar que FPDF existe
            if (!class_exists('FPDF')) {
                Log::error('FPDF no está instalado');
                return response('<html><body style="font-family: Arial; padding: 40px;">
                    <h1 style="color: red;">❌ FPDF no está instalado</h1>
                    <p>Ejecuta: <code>composer require setasign/fpdf</code></p>
                </body></html>', 500);
            }

            // Conectar a Oracle usando OCI
            Log::info('Conectando a Oracle con OCI...');
            $conn = $this->getOracleConnection();

            // PASO 1: Obtener los datos del recibo desde Oracle
            Log::info('Consultando recibo en Oracle...');
            
            // El recibo se busca acotado al legajo del usuario autenticado: sin esto
            // cualquier empleado logueado podria descargar el recibo de otro
            // probando numeros en la URL.
            $legajoAutenticado = Auth::user()->LEGAJO;

            // Ademas del legajo, se acota a los recibos ya cobrados segun la
            // fecha de corte que define RRHH desde INASI.
            $hasta = ReciboVisibilidad::fechaCorte();

            $sqlRecibo = "SELECT * FROM PER_RECIBO_CAB
                          WHERE LEGAJO = :legajo
                          AND NRO_RECIBO = :nroRecibo
                          AND ANIO = :anio
                          AND MES = :mes
                          AND TIPO_LIQ = :tipoLiq
                          AND " . ReciboVisibilidad::condicionSql();

            $stmtRecibo = oci_parse($conn, $sqlRecibo);
            oci_bind_by_name($stmtRecibo, ':legajo', $legajoAutenticado);
            oci_bind_by_name($stmtRecibo, ':nroRecibo', $nroRecibo);
            oci_bind_by_name($stmtRecibo, ':anio', $anio);
            oci_bind_by_name($stmtRecibo, ':mes', $mes);
            oci_bind_by_name($stmtRecibo, ':tipoLiq', $tipoLiq);
            oci_bind_by_name($stmtRecibo, ':hasta', $hasta);

            oci_execute($stmtRecibo);

            $recibo = oci_fetch_array($stmtRecibo, OCI_ASSOC);

            if (!$recibo) {
                oci_free_statement($stmtRecibo);
                oci_close($conn);

                Log::warning('Recibo no encontrado en Oracle', [
                    'legajo_solicitante' => $legajoAutenticado,
                    'nroRecibo'          => $nroRecibo,
                    'anio'               => $anio,
                    'mes'                => $mes,
                    'tipoLiq'            => $tipoLiq,
                ]);
                return response('<html><body style="font-family: Arial; padding: 40px;">
                    <h1 style="color: red;">❌ Recibo no encontrado</h1>
                    <ul>
                        <li>Número: ' . $nroRecibo . '</li>
                        <li>Año: ' . $anio . '</li>
                        <li>Mes: ' . $mes . '</li>
                        <li>Tipo: ' . htmlspecialchars($tipoLiq) . '</li>
                    </ul>
                    <p><a href="javascript:history.back()">← Volver</a></p>
                </body></html>', 404);
            }

            $legajo = $recibo['LEGAJO'];
            Log::info('Recibo encontrado', ['legajo' => $legajo]);

            // PASO 2: Obtener datos del empleado desde MySQL
            Log::info('Consultando empleado en MySQL...');
            
            $persona = DB::connection('mysql')->table('in_maestro')
                ->where('LEGAJO', $legajo)
                ->first();

            if (!$persona) {
                oci_free_statement($stmtRecibo);
                oci_close($conn);
                
                Log::warning('Empleado no encontrado en MySQL');
                return response('<html><body style="font-family: Arial; padding: 40px;">
                    <h1 style="color: red;">❌ Empleado no encontrado</h1>
                    <p>Legajo: ' . $legajo . '</p>
                    <p><a href="javascript:history.back()">← Volver</a></p>
                </body></html>', 404);
            }

            $personaArray = json_decode(json_encode($persona), true);

            // Log para ver qué datos tenemos
            Log::info('Datos persona para PDF', [
                'apellido' => $personaArray['APELLIDO'] ?? 'NO_EXISTE',
                'nombres' => $personaArray['NOMBRES'] ?? 'NO_EXISTE'
            ]);
            
            Log::info('Empleado encontrado', [
                'claves_disponibles' => array_keys($personaArray),
                'datos_persona' => $personaArray
            ]);

            // NUEVO: Obtener datos adicionales desde Oracle (PER_VIS_RECIBOS_CAB)
            Log::info('Consultando datos adicionales en Oracle...');
            
            $sqlPersona = "SELECT DES_CATEGORIA, JURISDICCION, DES_TIPO_PLANTA, COD_CATEGORIA, FECH_ANTIG 
                        FROM PER_VIS_RECIBOS_CAB 
                        WHERE LEGAJO = :legajo 
                        AND ANIO = :anio 
                        AND MES = :mes 
                        AND TIPO_LIQ = :tipoLiq";
            
            $stmtPersona = oci_parse($conn, $sqlPersona);
            oci_bind_by_name($stmtPersona, ':legajo', $legajo);
            oci_bind_by_name($stmtPersona, ':anio', $anio);
            oci_bind_by_name($stmtPersona, ':mes', $mes);
            oci_bind_by_name($stmtPersona, ':tipoLiq', $tipoLiq);
            
            oci_execute($stmtPersona);
            
            $datosOracle = oci_fetch_array($stmtPersona, OCI_ASSOC);
            
            if ($datosOracle) {
                // Combinar datos de MySQL con Oracle
                $personaArray = array_merge($personaArray, $datosOracle);
                Log::info('Datos adicionales de Oracle combinados', [
                    'des_categoria' => $datosOracle['DES_CATEGORIA'] ?? 'N/A',
                    'jurisdiccion' => $datosOracle['JURISDICCION'] ?? 'N/A'
                ]);
            }
            
            oci_free_statement($stmtPersona);

            // PASO 3: Obtener conceptos desde Oracle
            // IMPORTANTE: PER_RECIBO_DET no tiene columna TIPO_LIQ
            // Usar la misma consulta que funciona en ReciboDetalle.php
            Log::info('Consultando conceptos en Oracle...');
            
            $sqlConceptos = "SELECT * FROM PER_RECIBO_DET 
                             WHERE NRO_RECIBO = :nroRecibo 
                             AND ANIO = :anio 
                             AND CONCEPTO < 90000 
                             ORDER BY ORDEN";
            
            $stmtConceptos = oci_parse($conn, $sqlConceptos);
            oci_bind_by_name($stmtConceptos, ':nroRecibo', $nroRecibo);
            oci_bind_by_name($stmtConceptos, ':anio', $anio);
            
            oci_execute($stmtConceptos);
            
            // Obtener todos los conceptos
            $conceptos = [];
            while ($row = oci_fetch_array($stmtConceptos, OCI_ASSOC)) {
                $conceptos[] = $row;
            }
            
            Log::info('Conceptos encontrados', ['cantidad' => count($conceptos)]);

            // Liberar recursos de Oracle
            oci_free_statement($stmtRecibo);
            oci_free_statement($stmtConceptos);
            oci_close($conn);

            // PASO 4: Verificar que ReciboPDF existe
            if (!class_exists('App\Services\ReciboPDF')) {
                Log::error('Clase ReciboPDF no encontrada');
                return response('<html><body style="font-family: Arial; padding: 40px;">
                    <h1 style="color: red;">❌ ReciboPDF no encontrada</h1>
                    <p>Verifica que existe: <code>app/Services/ReciboPDF.php</code></p>
                </body></html>', 500);
            }

            // PASO 5: Crear PDF
            Log::info('Creando instancia de ReciboPDF...');
            
            $pdf = new ReciboPDF($recibo, $personaArray, $conceptos);
            
            Log::info('Generando contenido del PDF...');
            
            $pdf->generarContenido();
            
            Log::info('Obteniendo output del PDF...');
            
            $output = $pdf->Output('S');
            
            Log::info('PDF generado exitosamente', ['tamaño' => strlen($output) . ' bytes']);

            // PASO 6: Retornar el PDF
            $filename = 'recibo_' . $legajo . '_' . $mes . '_' . $anio . '.pdf';
            
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
            
        } catch (Exception $e) {
            Log::error('Error al generar PDF', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response('<html><body style="font-family: Arial; padding: 40px;">
                <h1 style="color: red;">❌ Error al Generar PDF</h1>
                <p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                <p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . '</p>
                <p><strong>Línea:</strong> ' . $e->getLine() . '</p>
                <p>Revisa los logs en: <code>storage/logs/laravel.log</code></p>
                <p><a href="javascript:history.back()">← Volver</a></p>
            </body></html>', 500);
        }
    }
}