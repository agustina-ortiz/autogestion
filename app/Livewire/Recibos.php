<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use PDO;
use PDOException;

class Recibos extends Component
{
    use WithPagination;

    public $perPage = 5;
    protected $paginationTheme = 'tailwind';

    public function getRecibosData()
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
            $page = $this->getPage();
            $offset = ($page - 1) * $this->perPage;
            
            // Consulta para contar total de registros
            $sqlCount = "SELECT COUNT(*) as total FROM per_recibo_cab 
                         WHERE legajo = :legajo
                         AND fecha_emision < (CURRENT_DATE - 1)";
            
            $stmtCount = oci_parse($conn, $sqlCount);
            oci_bind_by_name($stmtCount, ':legajo', $legajo);
            oci_execute($stmtCount);
            $rowCount = oci_fetch_array($stmtCount, OCI_ASSOC);
            $totalRecords = $rowCount['TOTAL'];
            
            // Consulta para obtener registros paginados
            $sql = "SELECT * FROM (
                        SELECT a.*, ROWNUM rnum FROM (
                            SELECT * FROM per_recibo_cab 
                            WHERE legajo = :legajo
                            AND fecha_emision < (CURRENT_DATE - 1)
                            ORDER BY anio DESC, nro_recibo DESC
                        ) a WHERE ROWNUM <= :end_row
                    ) WHERE rnum > :start_row";
            
            $stmt = oci_parse($conn, $sql);
            
            $end_row = $offset + $this->perPage;
            $start_row = $offset;
            
            oci_bind_by_name($stmt, ':legajo', $legajo);
            oci_bind_by_name($stmt, ':end_row', $end_row);
            oci_bind_by_name($stmt, ':start_row', $start_row);
            
            oci_execute($stmt);
            
            // Obtener todos los resultados
            $rows = [];
            while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $rows[] = $row;
            }
            
            // Liberar recursos
            oci_free_statement($stmtCount);
            oci_free_statement($stmt);
            oci_close($conn);
            
            return [
                'rows' => $rows,
                'totalRecords' => $totalRecords,
                'offset' => $offset
            ];
            
        } catch (Exception $e) {
            session()->flash('error', 'Error Oracle: ' . $e->getMessage());
            return [
                'rows' => [],
                'totalRecords' => 0,
                'offset' => 0
            ];
        }
    }

    public function render()
    {
        $data = $this->getRecibosData();
        $totalPages = ceil($data['totalRecords'] / $this->perPage);
        $currentPage = $this->getPage();

        return view('livewire.recibos', [
            'rows' => $data['rows'],
            'totalRecords' => $data['totalRecords'],
            'offset' => $data['offset'],
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ])->layout('components.layouts.autogestion');
    }
}