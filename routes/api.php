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

Route::post('login', 'UserController@login'); // Login
Route::post('register', 'UserController@register'); // Criação de usuário

Route::group(['middleware' => ['auth:api']], function () {

    Route::prefix('users')->group(function () { // API para requisições de usuário
        Route::get('/', 'UserController@getUser');
    });

    Route::prefix('ml')->group(function () { // API para requisições do Mercado Livre
        Route::get('/sales', 'MLController@getAllSales'); // Buscar vendas
        Route::post('/code', 'MLController@insertCode'); // Inserir código
    });

    Route::prefix('seller')->group(function () { // API para requisições de usuário
        Route::get('/verify/withdrawal/status', 'SellerController@verifyWithdrawalStatus'); // Verifica status da retirada
        Route::post('/verify/destination', 'SellerController@verifyPackageHasArrived'); // Verifica se encomenda chegou no destino
        Route::post('/request/withdrawal', 'SellerController@requestWithdrawal'); // Solicitar retirada
    });

    Route::prefix('driver')->group(function () { // API para requisições do motorista
        Route::get('/search/seller', 'DriverController@searchSeller'); // Busca vendedor
        Route::post('/coordinates', 'DriverController@getCoordinates'); // Buscar coordenadas
        Route::put('/accept/running', 'DriverController@acceptRunning'); // Aceita corrida
    });


});
