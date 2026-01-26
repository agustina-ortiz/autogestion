<?php

namespace App\Helpers;

class FotoHelper
{
    public static function obtenerUrlFoto($legajo)
    {
        $legajo = str_pad($legajo, 8, '0', STR_PAD_LEFT);
        $nombreArchivo = $legajo . '.jpg';
        
        // Ruta directa al directorio público
        $directorio = public_path('fotos-licencias/fotos-empleados');
        $rutaArchivo = $directorio . '/' . $nombreArchivo;
        $marcadorEliminada = $directorio . '/' . $legajo . '_eliminada.txt';
        
        // Si existe el marcador de foto eliminada, mostrar imagen por defecto
        if (file_exists($marcadorEliminada)) {
            return asset('images/no-foto.png');
        }
        
        // Verificar si existe la foto
        if (file_exists($rutaArchivo)) {
            return asset('fotos-licencias/fotos-empleados/' . $nombreArchivo) . '?t=' . time();
        }
        
        return asset('images/no-foto.png');
    }
    
    public static function tieneFoto($legajo)
    {
        $legajo = str_pad($legajo, 8, '0', STR_PAD_LEFT);
        $nombreArchivo = $legajo . '.jpg';
        
        // Ruta directa al directorio público
        $directorio = public_path('fotos-licencias/fotos-empleados');
        $rutaArchivo = $directorio . '/' . $nombreArchivo;
        $marcadorEliminada = $directorio . '/' . $legajo . '_eliminada.txt';
        
        if (file_exists($marcadorEliminada)) {
            return false;
        }
        
        return file_exists($rutaArchivo);
    }
}