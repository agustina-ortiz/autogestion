<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Asistencias extends Component
{
    public $fechaDesde;
    public $fechaHasta;
    public $empleado;
    public $fichadas = [];
    public $novedades = [];
    public $legajo;

    public function mount()
    {
        // Por defecto: último mes
        $this->fechaHasta = Carbon::now()->format('Y-m-d');
        $this->fechaDesde = Carbon::now()->subMonth()->format('Y-m-d');
        
        // Empleado logueado (ajustar según tu modelo User)
        $this->empleado = auth()->user();
        $this->legajo = $this->empleado->LEGAJO; // Asegúrate que el User tenga el campo legajo
        
        // Cargar datos iniciales
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->cargarFichadas();
        $this->cargarNovedades();
    }

    private function cargarFichadas()
    {
        $desde = $this->fechaDesde;
        $hasta = $this->fechaHasta;
        $legajo = $this->legajo;

        // Primera parte: fichadas normales
        $fichadasNormales = DB::table('munimer_inasi.in_horas as h')
            ->join('munimer_inasi.in_maestro as m', function($join) use ($legajo) {
                $join->on('m.tarjeta', '=', 'h.codtar')
                    ->where('m.legajo', '=', $legajo);
            })
            ->select(
                'h.fecha as fecha',
                'h.hora as hora', 
                'h.codtar as codtar',
                DB::raw("' ' as certifi"),
                DB::raw("'F' as tipo")
            )
            ->where('m.legajo', $legajo)
            ->whereBetween('h.fecha', [$desde, $hasta])
            ->get();

        // Segunda parte: inasistencias con certificados
        $inasistencias = DB::table('munimer_inasi.in_movimie as h')
            ->leftJoin('munimer_inasi.in_codigo as c', 'c.codigo', '=', 'h.codigo')
            ->leftJoin('munimer_inasi.in_maestro as m', 'h.legajo', '=', 'm.legajo')
            ->select(
                'h.fecinasi as fecha',
                'c.nombre as hora',
                'm.tarjeta as codtar',
                DB::raw("IF(h.certifi, CONCAT('http://200.5.80.125/licencias/public/fotos-licencias/certificados/', LPAD(h.legajo,8,'0'), '-', REPLACE(h.fecinasi,'-',''), '.jpg'), ' ') as certifi"),
                DB::raw("'I' as tipo")
            )
            ->where('h.legajo', $legajo)
            ->whereBetween('h.fecinasi', [$desde, $hasta])
            ->get();

        // Combinar ambas colecciones
        $this->fichadas = $fichadasNormales->merge($inasistencias)
            ->sortBy([
                ['fecha', 'asc'],
                ['hora', 'asc']
            ])
            ->values()
            ->toArray();
    }

    private function cargarNovedades()
    {
        $year = Carbon::parse($this->fechaDesde)->year;
        $legajo = $this->legajo;

        $sql = "SELECT V.CODIGO, V.NOMBRE, 
                       SUM(V.ENERO) AS ENE, SUM(V.FEB) AS FEB, 
                       SUM(V.MARZ) AS MAR, SUM(V.ABRIL) AS ABR,
                       SUM(V.MAYO) AS MAY, SUM(V.JUNIO) AS JUN,
                       SUM(V.JULIO) AS JUL, SUM(V.AGOST) AS AGO,
                       SUM(V.SEPT) AS SEP, SUM(V.OCTU) AS OCT,
                       SUM(V.NOV) AS NOV, SUM(V.DIC) AS DIC 
                FROM ( 
                  SELECT C.CODIGO, C.NOMBRE, COUNT(LEGAJO) AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 1 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL 
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, COUNT(LEGAJO) AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 2 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, COUNT(LEGAJO) AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 3 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, COUNT(LEGAJO) AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 4 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, COUNT(LEGAJO) AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 5 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, COUNT(LEGAJO) AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 6 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, COUNT(LEGAJO) AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 7 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, COUNT(LEGAJO) AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 8 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, COUNT(LEGAJO) AS SEPT, 0 AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 9 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, COUNT(LEGAJO) AS OCTU, 0 AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 10 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, COUNT(LEGAJO) AS NOV, 0 AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 11 
                  GROUP BY C.CODIGO, C.NOMBRE 
                  UNION ALL  
                  SELECT C.CODIGO, C.NOMBRE, 0 AS ENERO, 0 AS FEB, 0 AS MARZ, 0 AS ABRIL, 0 AS MAYO, 0 AS JUNIO, 0 AS JULIO, 0 AS AGOST, 0 AS SEPT, 0 AS OCTU, 0 AS NOV, COUNT(LEGAJO) AS DIC 
                  FROM munimer_inasi.in_movimie m LEFT OUTER JOIN munimer_inasi.in_codigo C ON C.CODIGO = m.CODIGO 
                  WHERE YEAR(m.fecinasi) = {$year} AND m.LEGAJO = {$legajo} AND MONTH(m.FECINASI) = 12 
                  GROUP BY C.CODIGO, C.NOMBRE  
                ) V 
                GROUP BY V.CODIGO, V.NOMBRE";

        $this->novedades = DB::table(DB::raw("({$sql}) res"))
            ->select(
                "res.codigo", "res.nombre", "res.ene", "res.feb", "res.mar",
                "res.abr", "res.may", "res.jun", "res.jul", "res.ago", "res.sep",
                "res.oct", "res.nov", "res.dic"
            )
            ->orderBy("res.codigo")
            ->get()
            ->toArray();
    }

    public function mostrar()
    {
        $this->validate([
            'fechaDesde' => 'required|date',
            'fechaHasta' => 'required|date|after_or_equal:fechaDesde',
        ]);

        $this->cargarDatos();
    }

    public function render()
    {
        return view('livewire.asistencias')->layout('components.layouts.autogestion');
    }
}