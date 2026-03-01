<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('compra_retenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained()->cascadeOnDelete();
            $table->string('tipo');
            $table->decimal('porcentaje', 5, 2);
            $table->decimal('base', 10, 2);
            $table->decimal('valor', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('compra_retenciones'); }
};
