<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modelo_contable_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->enum('modulo', ['VENTAS', 'COMPRAS']);
            $table->string('campo');
            $table->unsignedBigInteger('cuenta_contable_id');
            $table->foreign('cuenta_contable_id')->references('id')->on('cuentas_contables');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('modelo_contable_detalles'); }
};
