<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'    => fake()->company(),
            'ruc'       => fake()->unique()->numerify('##########001'),
            'direccion' => fake()->streetAddress(),
            'telefono'  => fake()->phoneNumber(),
            'email'     => fake()->companyEmail(),
            'activa'    => true,
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => ['activa' => false]);
    }
}
