<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VentaPago extends Model {
    protected $table = 'venta_pagos';
    protected $fillable = ['venta_id','forma_pago_id','descripcion','monto'];
    public function formaPago() { return $this->belongsTo(FormaPago::class); }
}
