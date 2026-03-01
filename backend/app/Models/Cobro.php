<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cobro extends Model {
    protected $fillable = ['empresa_id','cliente_id','fecha','concepto','total','asiento_id'];
    protected $casts = ['fecha' => 'date'];
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function detalles() { return $this->hasMany(CobroDetalle::class); }
    public function pagos() { return $this->hasMany(CobroPago::class); }
    public function asiento() { return $this->belongsTo(Asiento::class); }
}
