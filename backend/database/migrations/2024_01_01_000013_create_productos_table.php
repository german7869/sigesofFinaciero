<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('codigo');
            $table->string('nombre');
            $table->string('categoria')->nullable();
            $table->decimal('precio', 10, 2);
            $table->unsignedBigInteger('impuesto_iva_id')->nullable();
            $table->foreign('impuesto_iva_id')->references('id')->on('impuestos_iva')->nullOnDelete();
            $table->unsignedBigInteger('cuenta_ingreso_id')->nullable();
            $table->foreign('cuenta_ingreso_id')->references('id')->on('cuentas_contables')->nullOnDelete();
            $table->unsignedBigInteger('cuenta_costo_id')->nullable();
            $table->foreign('cuenta_costo_id')->references('id')->on('cuentas_contables')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
