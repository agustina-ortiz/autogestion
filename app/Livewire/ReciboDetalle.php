<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Services\ReciboVisibilidad;
use Exception;
use PDO;
use PDOException;

class ReciboDetalle extends Component
{
    public $numero;
    public $anio;
    public $mes;
    public $tipo;
    public $recibo;
    public $error;
    public $conceptos = [];
    public $reciboVisualizacion = []; // nueva propiedad para guardar esa consulta

    public function mount($numero, $anio, $mes, $tipo)
    {
        $this->numero = $numero;
        $this->anio = $anio;
        $this->mes = $mes;
        $this->tipo = $tipo;
        
        $this->cargarRecibo();
    }

    public function cargarRecibo()
    {
        try {
            // Conexión Oracle con OCI
            $dbstr = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.0.22)(PORT=1521))(CONNECT_DATA=(SID=MMERC10G)))";
            $conn = oci_connect('autogestion', 'autgest19', $dbstr);
            
            if (!$conn) {
                $e = oci_error();
                throw new Exception("Error de conexión: " . $e['message']);
            }
            
            $legajo = Auth::user()->LEGAJO;
            
            // La cabecera se acota a los recibos ya cobrados: sin esto se podia
            // ver por URL un recibo que todavia no figura en el listado.
            $hasta = ReciboVisibilidad::fechaCorte();

            // Consulta cabecera
            $sqlCab = "SELECT * FROM per_recibo_cab
                        WHERE legajo = :legajo
                        AND nro_recibo = :numero
                        AND anio = :anio
                        AND mes = :mes
                        AND tipo_liq = :tipo
                        AND " . ReciboVisibilidad::condicionSql();

            $stmtCab = oci_parse($conn, $sqlCab);
            oci_bind_by_name($stmtCab, ':legajo', $legajo);
            oci_bind_by_name($stmtCab, ':numero', $this->numero);
            oci_bind_by_name($stmtCab, ':anio', $this->anio);
            oci_bind_by_name($stmtCab, ':mes', $this->mes);
            oci_bind_by_name($stmtCab, ':tipo', $this->tipo);
            oci_bind_by_name($stmtCab, ':hasta', $hasta);
            oci_execute($stmtCab);
            
            $this->recibo = oci_fetch_array($stmtCab, OCI_ASSOC);
            
            if (!$this->recibo) {
                oci_free_statement($stmtCab);
                oci_close($conn);
                abort(404, 'Recibo no encontrado');
            }
            
            oci_free_statement($stmtCab);
            
            // Consulta detalle de conceptos
            $sqlDet = "SELECT * FROM per_recibo_det 
                        WHERE nro_recibo = :numero 
                        AND anio = :anio 
                        AND concepto < 90000 
                        ORDER BY orden";
            
            $stmtDet = oci_parse($conn, $sqlDet);
            oci_bind_by_name($stmtDet, ':numero', $this->numero);
            oci_bind_by_name($stmtDet, ':anio', $this->anio);
            oci_execute($stmtDet);
            
            $this->conceptos = [];
            while ($row = oci_fetch_array($stmtDet, OCI_ASSOC)) {
                $this->conceptos[] = $row;
            }
            
            oci_free_statement($stmtDet);
            
            // Consulta visualización
            $sqlVis = "SELECT * FROM per_vis_recibos_cab 
                        WHERE legajo = :legajo
                        AND anio = :anio
                        AND mes = :mes
                        AND tipo_liq = :tipo";
            
            $stmtVis = oci_parse($conn, $sqlVis);
            oci_bind_by_name($stmtVis, ':legajo', $legajo);
            oci_bind_by_name($stmtVis, ':anio', $this->anio);
            oci_bind_by_name($stmtVis, ':mes', $this->mes);
            oci_bind_by_name($stmtVis, ':tipo', $this->tipo);
            oci_execute($stmtVis);
            
            $this->reciboVisualizacion = [];
            while ($row = oci_fetch_array($stmtVis, OCI_ASSOC)) {
                $this->reciboVisualizacion[] = $row;
            }
            
            oci_free_statement($stmtVis);
            oci_close($conn);
            
        } catch (Exception $e) {
            $this->error = 'Error al obtener el recibo: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.recibo-detalle')->layout('components.layouts.autogestion');
    }
}