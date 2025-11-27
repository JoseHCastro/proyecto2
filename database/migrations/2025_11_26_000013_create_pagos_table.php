<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('suscripcion_id')->constrained('suscripciones')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->date('fecha')->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, pagada
            $table->string('metodo')->nullable(); // efectivo, QR, tarjeta
            $table->date('vence')->nullable(); // Fecha de vencimiento
            $table->string('motivo')->nullable(); // Motivo del pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
