<?php

declare(strict_types=1);

namespace App\Enums;

enum PanelEnums: string
{
    case Admin = 'admin';

    /**
     * Get the path of the panel.
     */
    public function path(): string
    {
        return match ($this) {
            self::Admin => '',
        };
    }

    /**
     * Get the values of the enum.
     */
    public static function toArray(): array
    {
        return [
            self::Admin->value => 'Admin',
        ];
    }
}
