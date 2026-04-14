<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    private function makeMiddleware(): AdminMiddleware
    {
        return new AdminMiddleware();
    }

    private function makeRequest(): Request
    {
        return Request::create('/admin', 'GET');
    }

    private function nextHandler(): \Closure
    {
        return fn ($request) => new Response('OK', 200);
    }

    public function test_unauthenticated_user_gets_403(): void
    {
        Auth::logout();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->makeMiddleware()->handle($this->makeRequest(), $this->nextHandler());
    }

    public function test_authenticated_non_admin_gets_403(): void
    {
        $user = $this->createUser();
        Auth::login($user);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->makeMiddleware()->handle($this->makeRequest(), $this->nextHandler());
    }

    public function test_authenticated_admin_passes_through(): void
    {
        $admin = $this->createAdmin();
        Auth::login($admin);

        $response = $this->makeMiddleware()->handle($this->makeRequest(), $this->nextHandler());

        $this->assertEquals(200, $response->getStatusCode());
    }
}
