<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model {
    protected $table = 'compra_detalles';
    protected $fillable = ['compra_id','descripcion','cantidad','precio_unitario','subtotal','impuesto_iva_id','iva','total'];
    public function impuestoIva() { return $this->belongsTo(ImpuestoIva::class); }
}
