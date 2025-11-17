<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('in_tipo_movimiento', function (Blueprint $table) {
            $table->date('fecha_desde')->nullable()->after('tipo_movimiento');
            $table->date('fecha_hasta')->nullable()->after('fecha_desde');
            $table->date('fecha_acreditacion')->nullable()->after('fecha_hasta');
            $table->enum('forma_pago', ['deposito', 'cheque'])->default('deposito')->after('fecha_acreditacion');
            $table->decimal('importe_maximo', 10, 2)->nullable()->after('forma_pago');
        });

        // Actualizar datos existentes con valores de ejemplo
        // Deberás ajustar estas fechas según tus necesidades
        $hoy = now();
        $anio = $hoy->year;
        $mes = $hoy->month;

        // Adelanto de sueldo (ID 5)
        DB::table('in_tipo_movimiento')
            ->where('id', 5)
            ->update([
                'fecha_desde' => "{$anio}-{$mes}-01",
                'fecha_hasta' => "{$anio}-{$mes}-07",
                'fecha_acreditacion' => "{$anio}-{$mes}-10",
                'forma_pago' => 'deposito',
                'importe_maximo' => 250000.00,
            ]);

        // Sueldo por cheque (ID 6)
        DB::table('in_tipo_movimiento')
            ->where('id', 6)
            ->update([
                'fecha_desde' => "{$anio}-{$mes}-01",
                'fecha_hasta' => "{$anio}-{$mes}-27",
                'fecha_acreditacion' => null,
                'forma_pago' => 'cheque',
                'importe_maximo' => null,
            ]);

        // Adelanto por cheque (ID 7)
        DB::table('in_tipo_movimiento')
            ->where('id', 7)
            ->update([
                'fecha_desde' => "{$anio}-{$mes}-01",
                'fecha_hasta' => "{$anio}-{$mes}-07",
                'fecha_acreditacion' => "{$anio}-{$mes}-10",
                'forma_pago' => 'cheque',
                'importe_maximo' => 250000.00,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('in_tipo_movimiento', function (Blueprint $table) {
            $table->dropColumn(['fecha_desde', 'fecha_hasta', 'fecha_acreditacion', 'forma_pago', 'importe_maximo']);
        });
    }
};