<?php

namespace Tests\Unit;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_empresa_can_be_created_with_factory(): void
    {
        $empresa = Empresa::factory()->create(['nombre' => 'Test Corp']);

        $this->assertDatabaseHas('empresas', ['nombre' => 'Test Corp']);
        $this->assertTrue($empresa->activa);
    }

    public function test_empresa_inactiva_factory_state(): void
    {
        $empresa = Empresa::factory()->inactiva()->create();

        $this->assertFalse($empresa->activa);
    }

    public function test_empresa_belongs_to_many_users(): void
    {
        $empresa = Empresa::factory()->create();
        $user    = \App\Models\User::factory()->create();

        $empresa->usuarios()->attach($user->id, ['es_default' => true]);

        $this->assertCount(1, $empresa->usuarios);
        $this->assertTrue($empresa->usuarios->contains($user));
    }

    public function test_empresa_fillable_fields(): void
    {
        $data = [
            'nombre'    => 'Empresa Demo',
            'ruc'       => '1234567890001',
            'direccion' => 'Calle 1',
            'telefono'  => '0991234567',
            'email'     => 'empresa@demo.com',
            'activa'    => true,
        ];

        $empresa = Empresa::factory()->create($data);

        foreach ($data as $field => $value) {
            $this->assertSame($value, $empresa->$field);
        }
    }

    public function test_ruc_is_unique(): void
    {
        Empresa::factory()->create(['ruc' => '9999999999001']);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Empresa::factory()->create(['ruc' => '9999999999001']);
    }
}
