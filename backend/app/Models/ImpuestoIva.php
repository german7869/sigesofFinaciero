<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ImpuestoIva extends Model {
    protected $table = 'impuestos_iva';
    protected $fillable = ['empresa_id','codigo','nombre','porcentaje','cuenta_contable_id'];
    public function cuentaContable() { return $this->belongsTo(CuentaContable::class); }
}
