<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use PDO;
use PDOException;

#[Layout('layouts.app')]
class Recibos extends Component
{
    use WithPagination;

    public $perPage = 5;
    protected $paginationTheme = 'tailwind';

    public function getRecibosData()
    {
        try {
            // Datos de conexión
            $host = "10.0.0.22";
            $port = "1521";
            $sid = "MMERC10G";
            $username = "autogestion";
            $password = "autgest19";

            // DSN directo (DSN-less)
            $dsn = "odbc:Driver={Oracle in instantclient_19_28};Dbq={$host}:{$port}/{$sid};Uid={$username};Pwd={$password};";

            // Conexión PDO ODBC
            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $legajo = Auth::user()->LEGAJO;
            $page = $this->getPage();
            $offset = ($page - 1) * $this->perPage;

            // Consulta para contar total de registros
            $sqlCount = "SELECT COUNT(*) as total FROM per_recibo_cab 
                         WHERE legajo = :legajo
                         AND fecha_emision < (CURRENT_DATE - 1)";
            
            $stmtCount = $pdo->prepare($sqlCount);
            $stmtCount->execute(['legajo' => $legajo]);
            $totalRecords = $stmtCount->fetch(PDO::FETCH_ASSOC)['TOTAL'];

            // Consulta para obtener registros paginados
            $sql = "SELECT * FROM (
                        SELECT a.*, ROWNUM rnum FROM (
                            SELECT * FROM per_recibo_cab 
                            WHERE legajo = :legajo
                            AND fecha_emision < (CURRENT_DATE - 1)
                            ORDER BY anio DESC, nro_recibo DESC
                        ) a WHERE ROWNUM <= :end_row
                    ) WHERE rnum > :start_row";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'legajo' => $legajo,
                'end_row' => $offset + $this->perPage,
                'start_row' => $offset
            ]);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'rows' => $rows,
                'totalRecords' => $totalRecords,
                'offset' => $offset
            ];

        } catch (PDOException $e) {
            session()->flash('error', 'Error Oracle ODBC: ' . $e->getMessage());
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
        ]);
    }
}