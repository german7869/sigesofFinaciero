<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model {
    protected $table = 'venta_detalles';
    protected $fillable = ['venta_id','producto_id','descripcion','cantidad','precio_unitario','descuento','subtotal','impuesto_iva_id','iva','total'];
    public function producto() { return $this->belongsTo(Producto::class); }
    public function impuestoIva() { return $this->belongsTo(ImpuestoIva::class); }
}
