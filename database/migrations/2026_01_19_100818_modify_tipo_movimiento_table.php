<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('in_tipo_movimiento', function (Blueprint $table) {
            // Agregar el campo periodo después de tipo_movimiento
            $table->string('periodo', 50)->nullable()->after('tipo_movimiento');
            
            // Modificar el enum forma_pago para incluir 'ambos'
            $table->enum('forma_pago', ['deposito', 'cheque', 'ambos'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('in_tipo_movimiento', function (Blueprint $table) {
            // Eliminar el campo periodo
            $table->dropColumn('periodo');
            
            // Volver al enum original
            $table->enum('forma_pago', ['deposito', 'cheque'])->nullable()->change();
        });
    }
};