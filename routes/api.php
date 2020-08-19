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

Route::apiResource('users', 'UserController');
Route::apiResource('currencies', 'CurrencyController')->except('destroy');
Route::name('transfers.')->group(function () {
    Route::get('', 'TransferController@index')->name('index');
    Route::post('make', 'TransferController@make')->name('make');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
