<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proveedor_id')->constrained();
            $table->unsignedBigInteger('documento_id')->nullable();
            $table->foreign('documento_id')->references('id')->on('documentos')->nullOnDelete();
            $table->string('numero');
            $table->date('fecha');
            $table->string('concepto')->nullable();
            $table->decimal('subtotal_15', 10, 2)->default(0);
            $table->decimal('subtotal_5', 10, 2)->default(0);
            $table->decimal('subtotal_0', 10, 2)->default(0);
            $table->decimal('iva_15', 10, 2)->default(0);
            $table->decimal('iva_5', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('saldo', 10, 2);
            $table->unsignedBigInteger('asiento_id')->nullable();
            $table->foreign('asiento_id')->references('id')->on('asientos')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('compras'); }
};
