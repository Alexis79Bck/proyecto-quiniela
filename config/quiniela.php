<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Quiniela Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the Quiniela application,
    | including scoring rules, prediction deadlines, and other settings.
    |
    */

    'scoring' => [
        'exact_score' => 3, // Points for exact score prediction
        'correct_result' => 1, // Points for correct match result (win/draw/loss)
        'no_points' => 0, // Points for incorrect prediction
    ],

    'deadlines' => [
        'prediction_deadline_hours' => 1, // Hours before match start when predictions close
    ],

    'tournament' => [
        'name' => 'FIFA World Cup 2026',
        'start_date' => '2026-06-11',
        'end_date' => '2026-07-19',
    ],

    'groups' => [
        'points_per_win' => 3,
        'points_per_draw' => 1,
        'points_per_loss' => 0,
    ],
];