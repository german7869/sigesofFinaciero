<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model {
    use SoftDeletes;
    protected $fillable = ['empresa_id','proveedor_id','documento_id','numero','fecha','concepto','subtotal_15','subtotal_5','subtotal_0','iva_15','iva_5','total','saldo','asiento_id'];
    protected $casts = ['fecha' => 'date'];
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function detalles() { return $this->hasMany(CompraDetalle::class); }
    public function retenciones() { return $this->hasMany(CompraRetencion::class); }
    public function pagos() { return $this->hasMany(CompraPago::class); }
    public function asiento() { return $this->belongsTo(Asiento::class); }
}
