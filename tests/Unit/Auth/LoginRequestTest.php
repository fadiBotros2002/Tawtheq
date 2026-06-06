<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_rules_require_email_and_password(): void
    {
        $validator = Validator::make([], (new LoginRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_validation_rejects_invalid_email_format(): void
    {
        $validator = Validator::make(
            ['email' => 'not-an-email', 'password' => 'secret'],
            (new LoginRequest)->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_throttle_key_is_based_on_lowercase_email_and_ip(): void
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'User@Example.com',
        ]);
        $request->server->set('REMOTE_ADDR', '192.168.1.1');

        $this->assertSame('user@example.com|192.168.1.1', $request->throttleKey());
    }

    public function test_authenticate_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $request = LoginRequest::create('/login', 'POST', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $request->setContainer($this->app);

        $request->authenticate();

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticate_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $request = LoginRequest::create('/login', 'POST', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        $request->setContainer($this->app);

        $this->expectException(ValidationException::class);

        $request->authenticate();
    }

    public function test_authenticate_is_blocked_after_too_many_failed_attempts(): void
    {
        $user = User::factory()->create();

        $request = LoginRequest::create('/login', 'POST', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        $request->server->set('REMOTE_ADDR', '10.0.0.1');
        $request->setContainer($this->app);

        RateLimiter::clear($request->throttleKey());

        for ($i = 0; $i < 6; $i++) {
            try {
                $request->authenticate();
            } catch (ValidationException) {
                // expected on failed login
            }
        }

        $this->expectException(ValidationException::class);

        $request->ensureIsNotRateLimited();
    }
}
