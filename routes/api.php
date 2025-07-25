<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login'])->middleware('validarLogin');
Route::post('/register', [UserController::class, 'register'])->middleware('validarUser');
Route::get('/me', [UserController::class, 'me'])->middleware('auth:api');

Route::group(['middleware' => 'auth:api', 'prefix' => 'empresas'], function () {
    Route::get('/', [EmpresaController::class, 'index']);
    Route::get('/activas', [EmpresaController::class, 'empresas']);
    Route::get('/{id}', [EmpresaController::class, 'show']);
    Route::post('/', [EmpresaController::class, 'store'])->middleware('validarSaveEmpresa');
    Route::put('/{id}', [EmpresaController::class, 'update'])->middleware('validarSaveEmpresa');
    Route::delete('/{id}', [EmpresaController::class, 'destroy']);
});