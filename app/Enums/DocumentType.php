<?php

namespace App\Enums;

enum DocumentType: string
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';

    public function label(): string
    {
        return match ($this) {
            self::Inbound => __('diwan.document_type.inbound'),
            self::Outbound => __('diwan.document_type.outbound'),
        };
    }
}
