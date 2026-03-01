<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AsientoDetalle extends Model {
    protected $table = 'asiento_detalles';
    protected $fillable = ['asiento_id','cuenta_contable_id','descripcion','debe','haber'];
    protected $casts = ['debe' => 'decimal:2', 'haber' => 'decimal:2'];
    public function cuenta() { return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id'); }
    public function asiento() { return $this->belongsTo(Asiento::class); }
}
