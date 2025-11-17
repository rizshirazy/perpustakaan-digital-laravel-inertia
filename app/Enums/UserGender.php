<?php

namespace App\Enums;

enum UserGender: string
{
    case MALE = 'laki-laki';
    case FEMALE = 'perempuan';

    public function label(): string
    {
        return match ($this) {
            self::MALE   => 'Laki-laki',
            self::FEMALE => 'Perempuan',
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
