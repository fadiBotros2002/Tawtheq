<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Verified = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('diwan.document_status.draft'),
            self::Verified => __('diwan.document_status.verified'),
        };
    }
}
