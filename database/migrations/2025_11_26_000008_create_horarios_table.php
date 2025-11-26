<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('instructores')->onDelete('cascade');
            $table->integer('dia_semana'); // 0=Domingo, 1=Lunes, etc.
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('capacidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
