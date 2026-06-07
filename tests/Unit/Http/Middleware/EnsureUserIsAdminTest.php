<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class EnsureUserIsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_pass_middleware(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureUserIsAdmin;
        $response = $middleware->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    public function test_regular_user_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureUserIsAdmin;

        $this->expectExceptionMessage('You do not have permission to access this page.');

        $middleware->handle($request, fn () => new Response('ok'));
    }

    public function test_guest_is_blocked(): void
    {
        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => null);

        $middleware = new EnsureUserIsAdmin;

        $this->expectExceptionMessage('You do not have permission to access this page.');

        $middleware->handle($request, fn () => new Response('ok'));
    }
}
