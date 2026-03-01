<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PermisoModulo extends Model {
    protected $table = 'permiso_modulos';
    protected $fillable = ['user_id','empresa_id','modulo'];
    public function user() { return $this->belongsTo(User::class); }
    public function empresa() { return $this->belongsTo(Empresa::class); }
}
