<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('ejercicio');
            $table->integer('series');
            $table->integer('repeticiones');
            $table->date('creada_en'); // Using date as per diagram, though timestamps() adds created_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutinas');
    }
};
