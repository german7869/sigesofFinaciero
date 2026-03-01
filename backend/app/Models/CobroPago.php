<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CobroPago extends Model {
    protected $table = 'cobro_pagos';
    protected $fillable = ['cobro_id','forma_pago_id','descripcion','monto'];
    public function formaPago() { return $this->belongsTo(FormaPago::class); }
}
