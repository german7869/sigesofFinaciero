<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ModeloContableDetalle extends Model {
    protected $table = 'modelo_contable_detalles';
    protected $fillable = ['empresa_id','modulo','campo','cuenta_contable_id'];
    public function cuentaContable() { return $this->belongsTo(CuentaContable::class); }
}
