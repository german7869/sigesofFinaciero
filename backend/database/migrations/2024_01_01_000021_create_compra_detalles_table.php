<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained()->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->unsignedBigInteger('impuesto_iva_id')->nullable();
            $table->foreign('impuesto_iva_id')->references('id')->on('impuestos_iva')->nullOnDelete();
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('compra_detalles'); }
};
