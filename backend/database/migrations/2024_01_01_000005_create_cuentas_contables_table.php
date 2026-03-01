<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cuentas_contables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('codigo');
            $table->string('descripcion');
            $table->integer('nivel');
            $table->enum('tipo', ['ACTIVO', 'PASIVO', 'PATRIMONIO', 'INGRESO', 'GASTO', 'COSTO']);
            $table->boolean('es_auxiliar')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['empresa_id', 'codigo']);
            $table->foreign('parent_id')->references('id')->on('cuentas_contables')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('cuentas_contables'); }
};
