<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserTest extends TestCase
{
    #[DataProvider('roleProvider')]
    public function test_admin_role_method(string $role, bool $isAdmin): void
    {
        $user = User::factory()->make(['role' => $role]);

        $this->assertSame($isAdmin, $user->isAdmin());
    }

    public static function roleProvider(): array
    {
        return [
            'admin' => ['admin', true],
            'user' => ['user', false],
        ];
    }
}
