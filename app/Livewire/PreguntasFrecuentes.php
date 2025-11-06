<?php
// app/Livewire/PreguntasFrecuentes.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreguntaFrecuente;

class PreguntasFrecuentes extends Component
{
    public $busqueda = '';
    public $preguntaExpandida = null;

    public function togglePregunta($id)
    {
        if ($this->preguntaExpandida === $id) {
            $this->preguntaExpandida = null;
        } else {
            $this->preguntaExpandida = $id;
        }
    }

    public function updatedBusqueda()
    {
        $this->preguntaExpandida = null;
    }

    public function limpiarBusqueda()
    {
        $this->busqueda = '';
        $this->preguntaExpandida = null;
    }

    public function render()
    {
        $preguntasFiltradas = PreguntaFrecuente::buscar($this->busqueda)->get();

        return view('livewire.preguntas-frecuentes', [
            'preguntasFiltradas' => $preguntasFiltradas
        ])->layout('components.layouts.autogestion');
    }
}