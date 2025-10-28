<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnticipoJubilatorio extends Component
{
    public $rows = [];
    public $perPage = 10;
    public $currentPage = 1;
    public $totalRecords = 0;
    public $totalPages = 0;
    public $offset = 0;

    public function mount()
    {
        $this->loadAnticipos();
    }

    public function loadAnticipos()
    {
        $legajo = Auth::user()->LEGAJO;

        // Obtener registros únicos agrupados por año, mes, sub y detalle
        $query = DB::connection('mysql')
            ->table('in_anti_jubila_liq')
            ->select('ano', 'mes', 'sub', 'detalle', DB::raw('SUM(neto) as importe_neto'))
            ->where('legajo', $legajo)
            ->groupBy('ano', 'mes', 'sub', 'detalle')
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc');

        // Obtener el total de registros
        $this->totalRecords = $query->count();

        // Calcular páginas
        $this->totalPages = ceil($this->totalRecords / $this->perPage);
        $this->offset = ($this->currentPage - 1) * $this->perPage;

        // Obtener los registros paginados
        $this->rows = $query
            ->offset($this->offset)
            ->limit($this->perPage)
            ->get()
            ->map(function ($row) {
                return [
                    'ANIO' => $row->ano,
                    'MES' => $row->mes,
                    'SUB' => $row->sub,
                    'TIPO_LIQ' => $row->detalle,
                    'LIQUIDO' => $row->importe_neto
                ];
            })
            ->toArray();
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadAnticipos();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadAnticipos();
        }
    }

    public function gotoPage($page)
    {
        $this->currentPage = $page;
        $this->loadAnticipos();
    }

    public function render()
    {
        return view('livewire.anticipo-jubilatorio')
            ->layout('components.layouts.autogestion')
            ->title('Anticipos Jubilatorios - Sistema Autogestión');
    }
}