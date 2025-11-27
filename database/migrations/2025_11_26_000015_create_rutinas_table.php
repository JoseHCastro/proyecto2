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
            $table->string('ejercicio', 200);
            $table->integer('series')->unsigned();
            $table->integer('repeticiones')->unsigned();
            $table->date('creada_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutinas');
    }
};
