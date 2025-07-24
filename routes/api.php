<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login'])->middleware('validarLogin');
Route::post('/register', [UserController::class, 'register'])->middleware('validarRegister');
Route::get('/me', [UserController::class, 'getUserLogin'])->middleware('auth:api');