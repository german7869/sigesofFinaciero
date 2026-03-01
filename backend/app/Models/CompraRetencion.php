<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CompraRetencion extends Model {
    protected $table = 'compra_retenciones';
    protected $fillable = ['compra_id','tipo','porcentaje','base','valor'];
}
