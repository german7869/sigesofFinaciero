<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(
            ['correo' => 'admin@gmail.com'],
            [
                'nombres' => 'Administrador',
                'correo' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'),
                'rol' => 'admin',
            ]
        );
    }
}
