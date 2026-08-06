<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Decide hasta que fecha de emision se le muestran los recibos al empleado.
 *
 * La fecha la define RRHH desde INASI cada vez que se acredita un pago. Segun
 * el entorno la fuente cambia, y se busca en este orden:
 *
 *   1. `corte_recibos` en munimer_inasinuevo (conexion mysql2), donde INASI
 *      nuevo esta publicado. Se sabe por DB_DATABASE_INASI_NUEVO en el .env.
 *   2. `in_fecha_recibos` en munimer_inasi (INASI viejo, conexion por defecto).
 *      Es la copia que mantiene INASI viejo para los entornos donde todavia no
 *      existe INASI nuevo.
 *   3. Si ninguna de las dos esta, se usa el criterio anterior a la fecha de
 *      corte: recibos emitidos hasta ayer. Es transitorio, para no dejar el
 *      listado en blanco mientras la tabla no exista.
 *
 * Las dos tablas tienen la columna `fecha_hasta`, pero no la misma forma:
 * `corte_recibos` es historial de solo alta con `id`, asi que la vigente es la
 * fila de mayor id; `in_fecha_recibos` es provisoria y guarda una unica fila
 * que INASI pisa cada vez que cambia la fecha. Por eso solo se ordena cuando
 * hay `id`. El portal solo lee, nunca escribe en ninguna de las dos.
 */
class ReciboVisibilidad
{
    /**
     * Fecha imposible que se usa cuando hay una fuente de corte definida pero
     * no se pudo leer. Deja la condicion sin coincidencias, de modo que ante
     * cualquier duda no se muestra ningun recibo en vez de mostrarlos todos.
     */
    private const SIN_CORTE = '1000-01-01';

    /**
     * Copia de la fecha de corte en INASI viejo, con la base calificada porque
     * se consulta por la conexion por defecto.
     */
    private const TABLA_INASI_VIEJO = 'munimer_inasi.in_fecha_recibos';

    /**
     * Fecha de corte vigente, en formato YYYY-MM-DD.
     */
    public static function fechaCorte(): string
    {
        if (self::inasiNuevoDisponible()) {
            return self::leerDe('mysql2', 'corte_recibos');
        }

        if (self::existeTablaInasiViejo()) {
            return self::leerDe('mysql', self::TABLA_INASI_VIEJO);
        }

        // Ninguna de las dos fuentes existe todavia en este entorno. Se mantiene
        // el criterio anterior en vez de fallar cerrado, que dejaria el listado
        // en blanco para todos los empleados.
        return now()->subDay()->format('Y-m-d');
    }

    /**
     * Ultima fecha de corte cargada en la tabla indicada.
     *
     * A partir de aca si se falla cerrado: la fuente existe, asi que un error
     * de lectura es una falla real y no un entorno sin configurar.
     */
    private static function leerDe(string $conexion, string $tabla): string
    {
        try {
            $consulta = DB::connection($conexion)->table($tabla);

            // corte_recibos es historial de solo alta, asi que la vigente es la
            // de mayor id. La copia de INASI viejo es una unica fila que se
            // pisa, no tiene id y no hace falta ordenarla.
            if (self::tieneColumnaId($conexion, $tabla)) {
                $consulta->orderByDesc('id');
            }

            $fecha = $consulta->value('fecha_hasta');
        } catch (Throwable $e) {
            Log::error('No se pudo leer la fecha de corte de recibos desde INASI', [
                'tabla' => $tabla,
                'conexion' => $conexion,
                'mensaje' => $e->getMessage(),
            ]);

            return self::SIN_CORTE;
        }

        if (!$fecha) {
            Log::warning('La tabla de corte de recibos esta vacia: no se muestra ningun recibo', [
                'tabla' => $tabla,
            ]);

            return self::SIN_CORTE;
        }

        return substr((string) $fecha, 0, 10);
    }

    /**
     * Si la tabla tiene columna `id`, o sea si es un historial ordenable.
     *
     * Ante la duda devuelve false: sin ordenar igual se lee la fila, mientras
     * que ordenar por una columna inexistente tiraria error y dejaria el
     * listado de recibos en blanco.
     */
    private static function tieneColumnaId(string $conexion, string $tabla): bool
    {
        try {
            return in_array('id', Schema::connection($conexion)->getColumnListing($tabla), true);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Si el entorno tiene configurado INASI nuevo (conexion mysql2).
     *
     * Se decide por DB_DATABASE_INASI_NUEVO en el .env: mientras esa base no
     * este publicada, la variable no se define. El dia que INASI nuevo suba, se
     * agregan las variables al .env y pasa a mandar corte_recibos, sin tocar
     * codigo.
     */
    private static function inasiNuevoDisponible(): bool
    {
        return filled(config('database.connections.mysql2.database'));
    }

    /**
     * Si INASI viejo ya tiene creada la copia de la fecha de corte.
     *
     * Mientras el equipo de INASI no la cree, la consulta tiraria "table
     * doesn't exist" en cada carga de la pantalla de recibos.
     */
    private static function existeTablaInasiViejo(): bool
    {
        try {
            return Schema::connection('mysql')->hasTable(self::TABLA_INASI_VIEJO);
        } catch (Throwable $e) {
            Log::error('No se pudo verificar la tabla de corte de recibos en INASI viejo', [
                'mensaje' => $e->getMessage(),
            ]);

            return false;
        }
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
