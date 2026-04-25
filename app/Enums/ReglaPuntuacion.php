<?php

namespace App\Enums;

enum ReglaPuntuacion: int
{
    case RESULTADO_EXACTO = 5;
    case SOLO_GANADOR = 3;
    case GOL_EQUIPO = 1;
    case NINGUNO = 0;
}
