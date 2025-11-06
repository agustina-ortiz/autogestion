<?php
// app/Livewire/PreguntasFrecuentes.php

namespace App\Livewire;

use Livewire\Component;

class PreguntasFrecuentes extends Component
{
    public $busqueda = '';
    public $preguntaExpandida = null;

    public $preguntas = [
        [
            'id' => 1,
            'pregunta' => '¿Qué es AUTOGESTIÓN?',
            'respuesta' => 'Es una página web que está conectada a la Subdirección de Recursos Humanos de la municipalidad, donde podés consultar tus recibos de sueldos, y otra información de utilidad para el empleado.'
        ],
        [
            'id' => 2,
            'pregunta' => '¿Desde dónde lo puedo ver?',
            'respuesta' => 'Lo podés ver tanto desde una computadora, como desde un teléfono celular que tenga conexión a Internet.'
        ],
        [
            'id' => 3,
            'pregunta' => '¿Quién puede ingresar a AUTOGESTIÓN?',
            'respuesta' => 'Pueden ingresar todo el personal municipal en relación de dependencia, que cobre sus haberes en el municipio.'
        ],
        [
            'id' => 4,
            'pregunta' => '¿Qué me va a mostrar esta página?',
            'respuesta' => 'Te va a mostrar el recibo de sueldo que elijas ver, qué días no fuiste a trabajar por presentar algún concepto de inasistencia, cuántos compensatorios pendientes te quedan, etc.'
        ],
        [
            'id' => 5,
            'pregunta' => '¿Puedo solicitar algún cambio por este canal?',
            'respuesta' => 'NO. Es solo un canal informativo. Podés hacer consultas por la sección CONTACTO pero eso no reemplaza los canales habituales.'
        ]
    ];

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

    public function getPreguntasFiltradasProperty()
    {
        if (empty($this->busqueda)) {
            return $this->preguntas;
        }

        $busqueda = mb_strtolower($this->busqueda);
        
        return array_filter($this->preguntas, function($pregunta) use ($busqueda) {
            $preguntaTexto = mb_strtolower($pregunta['pregunta']);
            $respuestaTexto = mb_strtolower($pregunta['respuesta']);
            
            return str_contains($preguntaTexto, $busqueda) || 
                   str_contains($respuestaTexto, $busqueda);
        });
    }

    public function render()
    {
        $preguntasFiltradas = $this->preguntas;

        if (!empty($this->busqueda)) {
            $busqueda = mb_strtolower($this->busqueda);
            
            $preguntasFiltradas = array_filter($this->preguntas, function($pregunta) use ($busqueda) {
                $preguntaTexto = mb_strtolower($pregunta['pregunta']);
                $respuestaTexto = mb_strtolower($pregunta['respuesta']);
                
                return str_contains($preguntaTexto, $busqueda) || 
                       str_contains($respuestaTexto, $busqueda);
            });
        }

        return view('livewire.preguntas-frecuentes', [
            'preguntasFiltradas' => $preguntasFiltradas
        ])->layout('components.layouts.autogestion');
    }
}