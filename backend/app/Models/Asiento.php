<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asiento extends Model {
    use SoftDeletes;
    protected $fillable = ['empresa_id','fecha','documento_id','numero','concepto','beneficiario','origen_modulo'];
    protected $casts = ['fecha' => 'date'];
    public function detalles() { return $this->hasMany(AsientoDetalle::class); }
    public function documento() { return $this->belongsTo(Documento::class); }
    public function empresa() { return $this->belongsTo(Empresa::class); }
}
