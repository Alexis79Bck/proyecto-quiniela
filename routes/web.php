<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return view('home');
});

//Route::resource('usuarios', UsuarioController::class);
Route::get('/', [UsuarioController::class, 'index']);