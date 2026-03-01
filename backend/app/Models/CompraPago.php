<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CompraPago extends Model {
    protected $table = 'compra_pagos';
    protected $fillable = ['compra_id','forma_pago_id','descripcion','monto'];
    public function formaPago() { return $this->belongsTo(FormaPago::class); }
}
