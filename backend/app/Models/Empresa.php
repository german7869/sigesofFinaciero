<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'activa',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_empresas')
            ->withPivot('es_default')
            ->withTimestamps();
    }
}
