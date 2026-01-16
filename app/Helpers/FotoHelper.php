<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FotoHelper
{
    public static function obtenerUrlFoto($legajo)
    {
        $legajo = str_pad($legajo, 8, '0', STR_PAD_LEFT);
        $nombreArchivo = $legajo . '.jpg';
        $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
        
        // Si existe el marcador de foto eliminada, mostrar imagen por defecto
        if (Storage::disk('public')->exists($marcadorEliminada)) {
            return asset('images/no-foto.png');
        }
        
        // Primero verificar si existe en storage local
        if (Storage::disk('public')->exists('fotos-empleados/' . $nombreArchivo)) {
            return asset('storage/fotos-empleados/' . $nombreArchivo) . '?t=' . time();
        }
        
        // Si no existe localmente, buscar en el servidor remoto
        $fotoUrl = 'https://autogestion.mercedes.gob.ar/fotos-licencias/fotos-empleados/' . $legajo . '.jpg';
        $tieneFoto = is_array(@getimagesize($fotoUrl));
        
        if ($tieneFoto) {
            return $fotoUrl;
        }
        
        return asset('images/no-foto.png');
    }
    
    public static function tieneFoto($legajo)
    {
        $legajo = str_pad($legajo, 8, '0', STR_PAD_LEFT);
        $nombreArchivo = $legajo . '.jpg';
        $marcadorEliminada = 'fotos-empleados/' . $legajo . '_eliminada.txt';
        
        if (Storage::disk('public')->exists($marcadorEliminada)) {
            return false;
        }
        
        if (Storage::disk('public')->exists('fotos-empleados/' . $nombreArchivo)) {
            return true;
        }
        
        $fotoUrl = 'https://autogestion.mercedes.gob.ar/fotos-licencias/fotos-empleados/' . $legajo . '.jpg';
        return is_array(@getimagesize($fotoUrl));
    }
}