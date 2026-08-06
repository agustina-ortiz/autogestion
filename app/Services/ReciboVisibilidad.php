<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Decide hasta que fecha de emision se le muestran los recibos al empleado.
 *
 * La fecha la define RRHH desde INASI cada vez que se acredita un pago, en la
 * tabla corte_recibos de munimer_inasinuevo (conexion mysql2). Esa tabla es
 * historial de solo alta: la fecha vigente es la de la fila de mayor id.
 *
 * El portal solo lee. Nunca escribe en corte_recibos.
 */
class ReciboVisibilidad
{
    /**
     * Fecha imposible que se usa cuando no hay corte definido o no se pudo
     * leer. Deja la condicion sin coincidencias, de modo que ante cualquier
     * duda no se muestra ningun recibo en vez de mostrarlos todos.
     */
    private const SIN_CORTE = '1000-01-01';

    /**
     * Fecha de corte vigente, en formato YYYY-MM-DD.
     */
    public static function fechaCorte(): string
    {
        // Mientras INASI nuevo no exista en este entorno no hay tabla que leer.
        // Se mantiene el criterio anterior (recibos emitidos hasta ayer) en vez
        // de fallar cerrado, que dejaria el listado en blanco para todos.
        if (!self::inasiNuevoDisponible()) {
            return now()->subDay()->format('Y-m-d');
        }

        try {
            $fecha = DB::connection('mysql2')
                ->table('corte_recibos')
                ->orderByDesc('id')
                ->value('fecha_hasta');
        } catch (Throwable $e) {
            Log::error('No se pudo leer la fecha de corte de recibos desde INASI', [
                'mensaje' => $e->getMessage(),
            ]);

            return self::SIN_CORTE;
        }

        if (!$fecha) {
            Log::warning('corte_recibos esta vacia: no se muestra ningun recibo');

            return self::SIN_CORTE;
        }

        return substr((string) $fecha, 0, 10);
    }

    /**
     * Si el entorno tiene configurado INASI nuevo (conexion mysql2).
     *
     * Se decide por DB_DATABASE_INASI_NUEVO en el .env: mientras esa base no
     * este publicada, la variable no se define y la tabla corte_recibos no
     * existe. El dia que INASI nuevo suba, se agregan las variables al .env y
     * el corte empieza a regir solo, sin tocar codigo.
     */
    private static function inasiNuevoDisponible(): bool
    {
        return filled(config('database.connections.mysql2.database'));
    }

    /**
     * Condicion SQL (Oracle) que acota los recibos a los ya cobrados.
     *
     * Va acompaniada del bind :hasta con el valor de fechaCorte(). Se usa en
     * las tres consultas que leen recibos para que no se desincronicen.
     */
    public static function condicionSql(): string
    {
        return "TRUNC(fecha_emision) <= TO_DATE(:hasta, 'YYYY-MM-DD')";
    }
}
