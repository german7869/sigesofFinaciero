<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('permiso_modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->enum('modulo', ['VENTAS', 'COMPRAS', 'CONTABILIDAD', 'BANCOS', 'SRI', 'REPORTES']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('permiso_modulos'); }
};
