<?php

namespace Tests\Unit;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_rol_field(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertSame('admin', $user->rol);
    }

    public function test_user_password_is_hidden_in_serialization(): void
    {
        $user = User::factory()->create();

        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_user_belongs_to_many_empresas(): void
    {
        $user    = User::factory()->create();
        $empresa = Empresa::factory()->create();

        $user->empresas()->attach($empresa->id, ['es_default' => false]);

        $this->assertCount(1, $user->empresas);
        $this->assertTrue($user->empresas->contains($empresa));
    }

    public function test_empresa_default_returns_pivot_default(): void
    {
        $user     = User::factory()->create();
        $other    = Empresa::factory()->create();
        $default  = Empresa::factory()->create();

        $user->empresas()->attach($other->id, ['es_default' => false]);
        $user->empresas()->attach($default->id, ['es_default' => true]);

        $result = $user->empresaDefault();

        $this->assertNotNull($result);
        $this->assertSame($default->id, $result->id);
    }

    public function test_empresa_default_falls_back_to_first_when_none_marked(): void
    {
        $user    = User::factory()->create();
        $empresa = Empresa::factory()->create();
        $user->empresas()->attach($empresa->id, ['es_default' => false]);

        $result = $user->empresaDefault();

        $this->assertNotNull($result);
        $this->assertSame($empresa->id, $result->id);
    }

    public function test_empresa_default_returns_null_when_no_empresas(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->empresaDefault());
    }

    public function test_factory_admin_state_sets_rol(): void
    {
        $user = User::factory()->admin()->make();
        $this->assertSame('admin', $user->rol);
    }

    public function test_factory_contador_state_sets_rol(): void
    {
        $user = User::factory()->contador()->make();
        $this->assertSame('contador', $user->rol);
    }

    public function test_factory_cajero_state_sets_rol(): void
    {
        $user = User::factory()->cajero()->make();
        $this->assertSame('cajero', $user->rol);
    }
}
