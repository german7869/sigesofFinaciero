<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('impuestos_iva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('codigo');
            $table->string('nombre');
            $table->decimal('porcentaje', 5, 2);
            $table->unsignedBigInteger('cuenta_contable_id')->nullable();
            $table->foreign('cuenta_contable_id')->references('id')->on('cuentas_contables')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('impuestos_iva'); }
};
