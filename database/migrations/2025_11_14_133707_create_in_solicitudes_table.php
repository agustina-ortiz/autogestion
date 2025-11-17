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
        Schema::create('in_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->integer('legajo')->constrained();
            $table->date('fecha_solicitud');
            $table->unsignedBigInteger('tipo_movimiento');
            $table->tinyInteger('origen')->default(1);
            $table->tinyInteger('estado')->default(1);
            $table->enum('forma_pago', ['deposito', 'cheque']);
            $table->decimal('importe', 10, 2)->nullable();

            // Foreign keys
            $table->foreign('legajo')
                  ->references('LEGAJO')
                  ->on('in_maestro');

            $table->foreign('tipo_movimiento')
                  ->references('id')
                  ->on('in_tipo_movimiento');

            // Índices para mejorar rendimiento
            $table->index('legajo');
            $table->index('fecha_solicitud');
            $table->index('estado');
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_solicitudes');
    }
};