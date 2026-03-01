<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CobroDetalle extends Model {
    protected $table = 'cobro_detalles';
    protected $fillable = ['cobro_id','venta_id','monto'];
    public function venta() { return $this->belongsTo(Venta::class); }
}
