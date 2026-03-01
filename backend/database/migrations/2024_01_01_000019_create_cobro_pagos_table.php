<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cobro_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cobro_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('forma_pago_id')->nullable();
            $table->foreign('forma_pago_id')->references('id')->on('formas_pago')->nullOnDelete();
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cobro_pagos'); }
};
