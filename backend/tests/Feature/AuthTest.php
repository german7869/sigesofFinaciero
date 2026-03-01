<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // POST /api/login
    // -----------------------------------------------------------------------

    public function test_login_with_valid_credentials_returns_token_and_user(): void
    {
        $user = User::factory()->admin()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'rol', 'empresa'],
            ])
            ->assertJsonPath('user.email', 'admin@example.com')
            ->assertJsonPath('user.rol', 'admin');

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_with_invalid_password_returns_422(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('correct'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Las credenciales no son correctas.');
    }

    public function test_login_with_nonexistent_email_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'noexiste@example.com',
            'password' => 'cualquiera',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_email_field(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'not-an-email',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_password_field(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_returns_empresa_when_user_has_one(): void
    {
        $empresa = Empresa::factory()->create(['nombre' => 'Empresa Test S.A.']);
        $user    = User::factory()->contador()->create([
            'email'    => 'contador@example.com',
            'password' => bcrypt('pass1234'),
        ]);
        $user->empresas()->attach($empresa->id, ['es_default' => true]);

        $response = $this->postJson('/api/login', [
            'email'    => 'contador@example.com',
            'password' => 'pass1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.empresa.nombre', 'Empresa Test S.A.')
            ->assertJsonPath('user.rol', 'contador');
    }

    public function test_login_returns_null_empresa_when_user_has_none(): void
    {
        User::factory()->create([
            'email'    => 'solo@example.com',
            'password' => bcrypt('pass1234'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'solo@example.com',
            'password' => 'pass1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.empresa', null);
    }

    // -----------------------------------------------------------------------
    // GET /api/me
    // -----------------------------------------------------------------------

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->admin()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'rol', 'empresa'])
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('rol', 'admin');
    }

    public function test_me_returns_empresa_when_user_has_one(): void
    {
        $empresa = Empresa::factory()->create(['nombre' => 'Mi Empresa']);
        $user    = User::factory()->contador()->create();
        $user->empresas()->attach($empresa->id, ['es_default' => true]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonPath('empresa.nombre', 'Mi Empresa');
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'No autenticado.');
    }

    public function test_me_with_expired_token_returns_401(): void
    {
        $response = $this->withToken('invalid-or-expired-token')
            ->getJson('/api/me');

        $response->assertStatus(401);
    }

    // -----------------------------------------------------------------------
    // POST /api/logout
    // -----------------------------------------------------------------------

    public function test_logout_returns_success_message(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Sesión cerrada correctamente.');
    }

    public function test_logout_revokes_token_so_me_returns_401(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('spa-token')->plainTextToken;

        // Token exists in the DB before logout
        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Logout using the token
        $this->withToken($token)->postJson('/api/logout')->assertStatus(200);

        // Token row must be deleted from the database
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    // -----------------------------------------------------------------------
    // EsAdmin middleware on /api/admin/* routes
    // -----------------------------------------------------------------------

    public function test_admin_route_group_rejects_non_admin(): void
    {
        // Add a test-only admin route during the test
        \Illuminate\Support\Facades\Route::middleware(['auth:sanctum', \App\Http\Middleware\EsAdmin::class])
            ->get('/api/admin/test-ping', fn () => response()->json(['ok' => true]));

        $contador = User::factory()->contador()->create();
        Sanctum::actingAs($contador);

        $response = $this->getJson('/api/admin/test-ping');

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Acceso restringido a administradores.');
    }

    public function test_admin_route_group_allows_admin(): void
    {
        \Illuminate\Support\Facades\Route::middleware(['auth:sanctum', \App\Http\Middleware\EsAdmin::class])
            ->get('/api/admin/test-ping', fn () => response()->json(['ok' => true]));

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/test-ping');

        $response->assertStatus(200)
            ->assertJsonPath('ok', true);
    }
}
