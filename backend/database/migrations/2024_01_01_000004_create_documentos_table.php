<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('modulo');
            $table->string('cod_establecimiento')->default('001');
            $table->string('punto_emision')->default('100');
            $table->integer('secuencia_actual')->default(0);
            $table->string('tipo_numeracion')->default('SECUENCIAL');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('documentos'); }
};
