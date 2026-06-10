<?php

namespace App\Enums;

enum CompetitionType: string
{
    case LEAGUE = 'LEAGUE';
    case CUP = 'CUP';
    case INTERNATIONAL = 'INTERNATIONAL';

    public static function label(string $value): string
    {
        return match ($value) {
            self::LEAGUE->value => 'Liga',
            self::CUP->value => 'Copa',
            self::INTERNATIONAL->value => 'Internacional',
            default => 'Desconocido'
        };
    }

}
