<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Crear empresa demo
        $empresa = Empresa::firstOrCreate(
            ['ruc' => '1234567890001'],
            [
                'nombre'    => 'Empresa Demo S.A.',
                'direccion' => 'Av. Principal 123',
                'telefono'  => '0991234567',
                'email'     => 'demo@empresa.com',
                'activa'    => true,
            ]
        );

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('admin1234'),
                'rol'      => 'admin',
            ]
        );
        $admin->empresas()->syncWithoutDetaching([
            $empresa->id => ['es_default' => true],
        ]);

        // Contador
        $contador = User::firstOrCreate(
            ['email' => 'contador@gmail.com'],
            [
                'name'     => 'Contador Principal',
                'password' => Hash::make('contador1234'),
                'rol'      => 'contador',
            ]
        );
        $contador->empresas()->syncWithoutDetaching([
            $empresa->id => ['es_default' => true],
        ]);
    }
}
