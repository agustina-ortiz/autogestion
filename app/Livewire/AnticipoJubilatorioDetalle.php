<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnticipoJubilatorioDetalle extends Component
{
    public $anio;
    public $mes;
    public $tipo;
    public $sub;
    
    public $empleado;
    public $conceptos = [];
    public $netoACobrar = 0;

    public function mount($anio, $mes, $tipo, $sub)
    {
        $this->anio = $anio;
        $this->mes = $mes;
        $this->tipo = $tipo;
        $this->sub = $sub;
        
        $this->loadData();
    }

    public function loadData()
    {
        $legajo = Auth::user()->LEGAJO;
        
        // Obtener datos del empleado
        $this->empleado = Auth::user();
        
        // Obtener los conceptos de liquidación
        $this->conceptos = DB::connection('mysql')
            ->table('in_anti_jubila_liq')
            ->where('legajo', $legajo)
            ->where('ano', $this->anio)
            ->where('mes', $this->mes)
            ->where('sub', $this->sub)
            ->where('detalle', $this->tipo)
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();
        
        // Calcular neto a cobrar
        if (count($this->conceptos) > 0) {
            $this->netoACobrar = array_sum(array_column($this->conceptos, 'neto'));
        }
        
        // Si no hay datos, mostrar error
        if (count($this->conceptos) === 0) {
            session()->flash('error', 'No se encontraron datos para este anticipo.');
        }
    }

    public function generarPdf()
    {
        // Lógica para generar PDF
        session()->flash('info', 'Funcionalidad de PDF en desarrollo');
    }

    public function render()
    {
        return view('livewire.anticipo-jubilatorio-detalle')
            ->layout('components.layouts.autogestion')
            ->title('Detalle de Anticipo - Sistema Autogestión');
    }
}