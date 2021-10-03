<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/contas', 'App\Http\Controllers\ContaController@criacao_conta');
Route::post('/contas/deposito', 'App\Http\Controllers\ContaController@deposito');
Route::put('/contas', 'App\Http\Controllers\ContaController@saque');
Route::get('/contas/saldo/{numero_conta}/{moeda?}', 'App\Http\Controllers\ContaController@saldo');
Route::get('/contas/{numero_conta}/{data_inicio?}/{data_final?}', 'App\Http\Controllers\ContaController@extrato');

