<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaContable extends Model {
    use SoftDeletes;
    protected $table = 'cuentas_contables';
    protected $fillable = ['empresa_id','parent_id','codigo','descripcion','nivel','tipo','es_auxiliar'];
    protected $casts = ['es_auxiliar' => 'boolean'];
    public function parent() { return $this->belongsTo(CuentaContable::class, 'parent_id'); }
    public function children() { return $this->hasMany(CuentaContable::class, 'parent_id'); }
    public function empresa() { return $this->belongsTo(Empresa::class); }
}
