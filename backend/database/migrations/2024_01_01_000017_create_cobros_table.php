<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained();
            $table->date('fecha');
            $table->string('concepto')->nullable();
            $table->decimal('total', 10, 2);
            $table->unsignedBigInteger('asiento_id')->nullable();
            $table->foreign('asiento_id')->references('id')->on('asientos')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cobros'); }
};
