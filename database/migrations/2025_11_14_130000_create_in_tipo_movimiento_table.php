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
        Schema::create('in_tipo_movimiento', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_movimiento', 100);
        });

        // Insertar los tipos de movimiento predefinidos
        DB::table('in_tipo_movimiento')->insert([
            ['id' => 5, 'tipo_movimiento' => 'Adelanto de sueldo'],
            ['id' => 6, 'tipo_movimiento' => 'Sueldo por cheque'],
            ['id' => 7, 'tipo_movimiento' => 'Aguinaldo por cheque'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_tipo_movimiento');
    }
};