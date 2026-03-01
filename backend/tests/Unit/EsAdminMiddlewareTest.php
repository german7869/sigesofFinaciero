<?php

namespace Tests\Unit;

use App\Http\Middleware\EsAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class EsAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function buildRequest(User $user): Request
    {
        $request = Request::create('/api/admin/test', 'GET');
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    public function test_admin_user_passes_through(): void
    {
        $admin = User::factory()->admin()->make();

        $middleware = new EsAdmin();
        $next       = fn ($req) => new Response('OK', 200);

        $response = $middleware->handle($this->buildRequest($admin), $next);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_contador_is_rejected_with_403(): void
    {
        $contador = User::factory()->contador()->make();

        $middleware = new EsAdmin();
        $next       = fn ($req) => new Response('OK', 200);

        $response = $middleware->handle($this->buildRequest($contador), $next);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString(
            'Acceso restringido a administradores.',
            $response->getContent()
        );
    }

    public function test_auxiliar_is_rejected_with_403(): void
    {
        $auxiliar = User::factory()->auxiliar()->make();

        $middleware = new EsAdmin();
        $next       = fn ($req) => new Response('OK', 200);

        $response = $middleware->handle($this->buildRequest($auxiliar), $next);

        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_cajero_is_rejected_with_403(): void
    {
        $cajero = User::factory()->cajero()->make();

        $middleware = new EsAdmin();
        $next       = fn ($req) => new Response('OK', 200);

        $response = $middleware->handle($this->buildRequest($cajero), $next);

        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_null_user_is_rejected_with_403(): void
    {
        $request = Request::create('/api/admin/test', 'GET');
        $request->setUserResolver(fn () => null);

        $middleware = new EsAdmin();
        $next       = fn ($req) => new Response('OK', 200);

        $response = $middleware->handle($request, $next);

        $this->assertSame(403, $response->getStatusCode());
    }
}
