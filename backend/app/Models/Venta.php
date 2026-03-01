<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model {
    use SoftDeletes;
    protected $fillable = ['empresa_id','cliente_id','documento_id','numero','fecha','concepto','subtotal_15','subtotal_5','subtotal_0','descuento','iva_15','iva_5','total','saldo','estado','asiento_id'];
    protected $casts = ['fecha' => 'date'];
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function detalles() { return $this->hasMany(VentaDetalle::class); }
    public function pagos() { return $this->hasMany(VentaPago::class); }
    public function asiento() { return $this->belongsTo(Asiento::class); }
    public function documento() { return $this->belongsTo(Documento::class); }
}
