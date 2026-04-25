<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuracion de Quiniela 
    |--------------------------------------------------------------------------
    |
    |
    */

    'deadline' => [
        'prediction_deadline_minutes' => 1, // Minutes before match start when predictions close
    ],

    'tournament_info' => [
        'name' => 'FIFA World Cup 2026',
        'start_date' => '2026-06-11',
        'end_date' => '2026-07-19',
    ],

    'points_rules_info' => [
        'exact_score' => 5,
        'correct_winner' => 3,
        'team_goals' => 1,
        'loss' => 0,
    ],
];