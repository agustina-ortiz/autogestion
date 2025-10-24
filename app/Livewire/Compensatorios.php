<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Maestro;
use App\Models\Compensatorio;
use App\Models\Movimiento;

class Compensatorios extends Component
{
    use WithPagination;

    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $legajo = Auth::user()->LEGAJO;
        $dias = 0;

        // Obtenemos el maestro y su tarjeta
        $maestro = Maestro::where('LEGAJO', $legajo)->first();

        // Siempre usamos el query builder con paginación
        $query = Movimiento::query()->whereRaw('1 = 0'); // Query vacío por defecto

        if ($maestro && $maestro->TARJETA) {
            // Total de días compensatorios (solo el más reciente)
            $dias = Compensatorio::totalDiasPorTarjeta($maestro->TARJETA);
            
            // Query real de movimientos - AJUSTADO A MAYÚSCULAS
            $query = Movimiento::compensatoriosTomados($legajo);
        }

        $rows = $query->paginate($this->perPage);

        return view('livewire.compensatorios', compact('rows', 'dias'))
            ->layout('layouts.app');
    }
}