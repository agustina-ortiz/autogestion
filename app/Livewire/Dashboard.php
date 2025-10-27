<?php
// app/Livewire/Dashboard.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Familia;

class Dashboard extends Component
{
    public $user;
    public $cantidadHijos = 0;
    public $esJubilado = false;
    public $noticias = [];

    public function mount()
    {
        $this->user = Auth::user();
        $legajo = $this->user->LEGAJO;
        
        // Contar hijos
        $this->cantidadHijos = Familia::contarHijos($this->user->LEGAJO);
    }

    public function descargarPlanilla()
    {
        // Lógica para descargar planilla
        // return response()->download(...);
    }

    public function abrirModalSubir()
    {
        $this->dispatch('open-upload-modal');
    }

    public function verAnticipo()
    {
        // Lógica para ver anticipo
        return redirect()->route('anticipo.show');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}