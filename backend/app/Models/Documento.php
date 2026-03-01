<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model {
    protected $fillable = ['empresa_id','codigo','descripcion','modulo','cod_establecimiento','punto_emision','secuencia_actual','tipo_numeracion'];
    public function empresa() { return $this->belongsTo(Empresa::class); }
    
    public function generarNumero(): string {
        $this->increment('secuencia_actual');
        $this->refresh();
        $sec = str_pad($this->secuencia_actual, 9, '0', STR_PAD_LEFT);
        return "{$this->cod_establecimiento}-{$this->punto_emision}-{$sec}";
    }
}
