<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['nombres', 'correo', 'password', 'rol'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];
    public function getAuthIdentifierName() { return 'correo'; }
    public function getEmailForPasswordReset() { return $this->correo; }
    public function empresas() { return $this->belongsToMany(Empresa::class, 'empresa_usuario'); }
    public function permisos() { return $this->hasMany(PermisoModulo::class); }
}
