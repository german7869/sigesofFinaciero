<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model {
    protected $table = 'formas_pago';
    protected $fillable = ['empresa_id','codigo','nombre','cuenta_contable_id'];
    public function cuentaContable() { return $this->belongsTo(CuentaContable::class); }
}
