<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notified_evaluaciones', function (Blueprint $table) {
            $table->unsignedInteger('legajo');
            $table->string('fecha', 30);
            $table->timestamp('notified_at')->useCurrent();
            $table->primary(['legajo', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notified_evaluaciones');
    }
};
