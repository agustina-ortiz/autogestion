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

    // Método estático para mostrar el detalle del recibo (se llama desde la ruta)
    public static function mostrarRecibo($numero, $anio, $mes, $tipo)
    {
        // Aquí puedes obtener los datos del recibo y mostrar la vista
        try {
            // Datos de conexión
            $host = "10.0.0.22";
            $port = "1521";
            $sid = "MMERC10G";
            $username = "autogestion";
            $password = "autgest19";

            $dsn = "odbc:Driver={Oracle in instantclient_19_28};Dbq={$host}:{$port}/{$sid};Uid={$username};Pwd={$password};";

            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $legajo = Auth::user()->LEGAJO;

            // Consulta para obtener el detalle del recibo
            $sql = "SELECT * FROM per_recibo_cab 
                    WHERE legajo = :legajo 
                    AND nro_recibo = :numero 
                    AND anio = :anio 
                    AND mes = :mes 
                    AND tipo_liq = :tipo";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'legajo' => $legajo,
                'numero' => $numero,
                'anio' => $anio,
                'mes' => $mes,
                'tipo' => $tipo
            ]);

            $recibo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$recibo) {
                abort(404, 'Recibo no encontrado');
            }

            return view('recibo-detalle', compact('recibo'));

        } catch (PDOException $e) {
            abort(500, 'Error al obtener el recibo: ' . $e->getMessage());
        }
    }

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