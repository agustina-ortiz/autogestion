<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Maestro;
use App\Models\Compensatorio;
use App\Models\Movimiento;

class Compensatorios extends Component
{
    public $perPage = 10;
    public $currentPage = 1;
    public $fechaDesde;
    public $fechaHasta;

    // Resetear paginación cuando cambian los filtros
    public function updatedFechaDesde()
    {
        $this->currentPage = 1;
    }

    public function updatedFechaHasta()
    {
        $this->currentPage = 1;
    }

    public function limpiarFiltros()
    {
        $this->fechaDesde = null;
        $this->fechaHasta = null;
        $this->currentPage = 1;
    }

    public function gotoPage($page)
    {
        $this->currentPage = $page;
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function render()
    {
        $legajo = Auth::user()->LEGAJO;
        $dias = 0;

        // Obtenemos el maestro y su tarjeta
        $maestro = Maestro::where('LEGAJO', $legajo)->first();

        // Siempre usamos el query builder
        $query = Movimiento::query()->whereRaw('1 = 0'); // Query vacío por defecto
        $totalRecords = 0;

        if ($maestro && $maestro->TARJETA) {
            // Total de días compensatorios (solo el más reciente)
            $dias = Compensatorio::totalDiasPorTarjeta($maestro->TARJETA);
            
            // Query real de movimientos
            $query = Movimiento::compensatoriosTomados($legajo);
            
            // Aplicar filtros de fecha
            if ($this->fechaDesde) {
                $query->whereDate('FECINASI', '>=', $this->fechaDesde);
            }
            
            if ($this->fechaHasta) {
                $query->whereDate('FECINASI', '<=', $this->fechaHasta);
            }

            // Contar total de registros
            $totalRecords = $query->count();
        }

        // Calcular offset y obtener registros paginados
        $offset = ($this->currentPage - 1) * $this->perPage;
        $rows = $query->offset($offset)->limit($this->perPage)->get();

        // Calcular total de páginas
        $totalPages = ceil($totalRecords / $this->perPage);

        return view('livewire.compensatorios', [
            'rows' => $rows,
            'dias' => $dias,
            'totalRecords' => $totalRecords,
            'offset' => $offset,
            'totalPages' => $totalPages,
            'currentPage' => $this->currentPage
        ])->layout('layouts.app');
    }
}