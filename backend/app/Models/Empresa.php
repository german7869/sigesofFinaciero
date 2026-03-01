<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model {
    use SoftDeletes;
    protected $fillable = ['ruc','razon_social','nombre_comercial','direccion','ciudad','email','telefono','logo'];
    public function usuarios() { return $this->belongsToMany(User::class, 'empresa_usuario'); }
    public function documentos() { return $this->hasMany(Documento::class); }
    public function cuentasContables() { return $this->hasMany(CuentaContable::class); }
}
