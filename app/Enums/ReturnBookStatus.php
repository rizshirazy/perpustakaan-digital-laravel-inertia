<?php

namespace App\Enums;

enum ReturnBookStatus: string
{
    case RETURNED = 'dikembalikan';
    case CHECKED  = 'pengecekan';
    case FINE     = 'denda';

    public function label(): string
    {
        return match ($this) {
            self::RETURNED => 'Dikembalikan',
            self::CHECKED  => 'Pengecekan',
            self::FINE     => 'Denda',
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
