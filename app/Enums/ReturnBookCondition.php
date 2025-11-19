<?php

namespace App\Enums;

enum ReturnBookCondition: string
{
    case GOOD    = 'baik';
    case DAMAGED = 'rusak';
    case LOST    = 'hilang';

    public function label(): string
    {
        return match ($this) {
            self::GOOD    => 'Baik',
            self::DAMAGED => 'Rusak',
            self::LOST    => 'Hilang',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
