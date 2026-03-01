<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('formas_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('codigo');
            $table->string('nombre');
            $table->unsignedBigInteger('cuenta_contable_id')->nullable();
            $table->foreign('cuenta_contable_id')->references('id')->on('cuentas_contables')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('formas_pago'); }
};
