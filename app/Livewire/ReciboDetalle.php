<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use PDO;
use PDOException;

#[Layout('layouts.app')]
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
            // Datos de conexión Oracle
            $host = "10.0.0.22";
            $port = "1521";
            $sid  = "MMERC10G";
            $username = "autogestion";
            $password = "autgest19";

            $dsn = "odbc:Driver={Oracle in instantclient_19_28};Dbq={$host}:{$port}/{$sid};Uid={$username};Pwd={$password};";

            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $legajo = Auth::user()->LEGAJO;

            // Consulta cabecera
            $sqlCab = "SELECT * FROM per_recibo_cab 
                        WHERE legajo = :legajo 
                        AND nro_recibo = :numero 
                        AND anio = :anio 
                        AND mes = :mes 
                        AND tipo_liq = :tipo";

            $stmtCab = $pdo->prepare($sqlCab);
            $stmtCab->execute([
                'legajo' => $legajo,
                'numero' => $this->numero,
                'anio'   => $this->anio,
                'mes'    => $this->mes,
                'tipo'   => $this->tipo,
            ]);

            $this->recibo = $stmtCab->fetch(PDO::FETCH_ASSOC);

            if (!$this->recibo) {
                abort(404, 'Recibo no encontrado');
            }

            // Consulta detalle de conceptos
            $sqlDet = "SELECT * FROM per_recibo_det 
                        WHERE nro_recibo = :numero 
                        AND anio = :anio 
                        AND concepto < 90000 
                        ORDER BY orden";

            $stmtDet = $pdo->prepare($sqlDet);
            $stmtDet->execute([
                'numero' => $this->numero,
                'anio'   => $this->anio
            ]);

            $this->conceptos = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            $sqlVis = "SELECT * FROM per_vis_recibos_cab 
                        WHERE legajo = :legajo
                        AND anio = :anio
                        AND mes = :mes
                        AND tipo_liq = :tipo";

            $stmtVis = $pdo->prepare($sqlVis);
            $stmtVis->execute([
                'legajo' => $legajo,
                'anio'   => $this->anio,
                'mes'    => $this->mes,
                'tipo'   => $this->tipo
            ]);

            $this->reciboVisualizacion = $stmtVis->fetchAll(PDO::FETCH_ASSOC);

            // dd([
            //     'recibo' => $this->recibo,
            //     'conceptos' => $this->conceptos,
            //     'reciboVisualizacion' => $this->reciboVisualizacion
            // ]);

        } catch (PDOException $e) {
            $this->error = 'Error al obtener el recibo: ' . $e->getMessage();
        }
    }

    public function abrirModalImpresion()
    {
        $this->dispatch('abrir-modal-impresion-recibo');
    }

    public function render()
    {
        return view('livewire.recibo-detalle');
    }
}