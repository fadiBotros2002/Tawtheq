<?php

namespace App\Enums;

enum TransactionCategory: string
{
    case Request = 'request';
    case Decision = 'decision';
    case Circular = 'circular';
    case Summons = 'summons';

    public function label(): string
    {
        return match ($this) {
            self::Request => 'طلب',
            self::Decision => 'قرار',
            self::Circular => 'تعميم',
            self::Summons => 'استدعاء',
        };
    }

    public function serialPrefix(): string
    {
        return match ($this) {
            self::Request => 'REQ',
            self::Decision => 'DEC',
            self::Circular => 'CIR',
            self::Summons => 'SUM',
        };
    }
}
