<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cobro_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cobro_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venta_id')->constrained();
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cobro_detalles'); }
};
