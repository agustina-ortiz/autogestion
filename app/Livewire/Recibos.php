<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Services\ReciboVisibilidad;
use Exception;

class Recibos extends Component
{
    use WithPagination;

    public $perPage = 5;
    public $isMobile = false;
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Detectar si es móvil al montar el componente
        $this->checkIfMobile();
    }

    public function checkIfMobile()
    {
        $userAgent = request()->header('User-Agent');
        $this->isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent);
        $this->perPage = $this->isMobile ? 10 : 5;
    }

    // Método público para actualizar desde el frontend
    public function updatePerPage($isMobile)
    {
        $this->isMobile = $isMobile;
        $this->perPage = $isMobile ? 10 : 5;
        $this->resetPage();
    }

    private function colorPorTipo(string $tipo): string
    {
        return match ($tipo) {
            'NOR' => '#16A34A', // verde  
            'ADI' => '#630b70', // azul
            'SAC' => '#ff6a00', // amarillo
            'DDN' => '#DB2777', // rosa
            'MUN' => '#da3f8d', // violeta
            'AD1' => '#059669', // verde oscuro
            'REY' => '#c1dc26', // rojo
            'AYU' => '#0284C7', // celeste
            'ASI' => '#4F46E5', // índigo   
            'AD2' => '#0D9488', // teal
            'AD3' => '#F97316', // naranja
            'AD4' => '#9333EA', // púrpura
            'ADH' => '#d77330', // rojo oscuro
            'HSM' => '#15803D', // verde fuerte
            'EGS' => '#A16207', // dorado
            'BON' => '#1D4ED8', // azul fuerte 
            'AD5' => '#402ffd', // índigo oscuro
            'HSE' => '#65A30D', // lima
            'NO2' => '#0F766E', // verde azulado
            default => '#6B7280', // gris (no contemplado)
        };
    }

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

            // Solo se muestran los recibos ya cobrados. La fecha la define RRHH
            // desde INASI el dia que se acredita el pago.
            $hasta = ReciboVisibilidad::fechaCorte();
            $visibles = ReciboVisibilidad::condicionSql();

            // Consulta para contar total de registros
            $sqlCount = "SELECT COUNT(*) as total FROM per_recibo_cab
                         WHERE legajo = :legajo
                         AND {$visibles}";

            $stmtCount = oci_parse($conn, $sqlCount);
            oci_bind_by_name($stmtCount, ':legajo', $legajo);
            oci_bind_by_name($stmtCount, ':hasta', $hasta);
            oci_execute($stmtCount);
            $rowCount = oci_fetch_array($stmtCount, OCI_ASSOC);
            $totalRecords = $rowCount['TOTAL'];
            
            // Consulta para obtener registros paginados
            $sql = "SELECT * FROM (
                        SELECT a.*, ROWNUM rnum FROM (
                            SELECT * FROM per_recibo_cab
                            WHERE legajo = :legajo
                            AND {$visibles}
                            ORDER BY anio DESC, nro_recibo DESC
                        ) a WHERE ROWNUM <= :end_row
                    ) WHERE rnum > :start_row";

            $stmt = oci_parse($conn, $sql);

            $end_row = $offset + $this->perPage;
            $start_row = $offset;

            oci_bind_by_name($stmt, ':legajo', $legajo);
            oci_bind_by_name($stmt, ':hasta', $hasta);
            oci_bind_by_name($stmt, ':end_row', $end_row);
            oci_bind_by_name($stmt, ':start_row', $start_row);
            
            oci_execute($stmt);
            
            // Obtener todos los resultados
            $rows = [];
            while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $row['COLOR_TIPO'] = $this->colorPorTipo($row['TIPO_LIQ']);
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
