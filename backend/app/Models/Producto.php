<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model {
    use SoftDeletes;
    protected $fillable = ['empresa_id','codigo','nombre','categoria','precio','impuesto_iva_id','cuenta_ingreso_id','cuenta_costo_id'];
    protected $casts = ['precio' => 'decimal:2'];
    public function impuestoIva() { return $this->belongsTo(ImpuestoIva::class); }
    public function cuentaIngreso() { return $this->belongsTo(CuentaContable::class, 'cuenta_ingreso_id'); }
    public function cuentaCosto() { return $this->belongsTo(CuentaContable::class, 'cuenta_costo_id'); }
}
