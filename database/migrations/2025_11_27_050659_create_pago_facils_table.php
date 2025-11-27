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
        Schema::create('pago_facils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Datos de la transacción
            $table->string('pedido_id')->unique(); // Nuestro ID interno (companyTransactionId)
            $table->string('pagofacil_transaction_id')->nullable(); // ID de PagoFácil

            // Detalles
            $table->decimal('monto', 10, 2);
            $table->longText('qr_image')->nullable(); // Cambiado a longText para soportar Base64 largos
            $table->tinyInteger('estado')->default(1); // 1: Pendiente, 2: Pagado, 3: Fallido, 5: Revisión

            // Datos del cliente usados
            $table->string('nombre_cliente')->nullable();
            $table->string('ci_cliente')->nullable();
            $table->string('telefono_cliente')->nullable();
            $table->string('email_cliente')->nullable();

            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_facils');
    }
};
