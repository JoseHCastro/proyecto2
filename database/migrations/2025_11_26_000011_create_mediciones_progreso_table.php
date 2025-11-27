<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mediciones_progreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('medido_en');
            $table->float('peso_kg');
            $table->float('estatura_cm');
            $table->float('grasa_corporal')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediciones_progreso');
    }
};
