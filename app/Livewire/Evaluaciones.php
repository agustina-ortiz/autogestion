<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluacion;

class Evaluaciones extends Component
{
    use WithPagination;

    public function render()
    {
        $legajo = Auth::user()->LEGAJO;

        $evaluaciones = Evaluacion::where('LEGAJO', $legajo)
            ->orderBy('FECHA', 'desc')
            ->paginate(5);

        return view('livewire.evaluaciones', compact('evaluaciones'))
            ->layout('components.layouts.autogestion');
    }
}
