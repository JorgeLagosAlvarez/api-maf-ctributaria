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

Route::apiResource('documents', 'DocumentController', [
    'only' => [
        'index', 'store', 'show', 'update'
    ]
])->middleware('auth.basic');
Route::get('b64/{file_name}', 'DocumentController@showb64')->name('documents.showb64')->middleware('auth.basic');

/*
Route::group(['prefix' => 'docs'], function() {
    Route::apiResource('boletas-honorarios', 'HonoraryTicketController', [
        'only' => ['index', 'store', 'show', 'update']
    ])->middleware('auth.basic');
});
*/

Route::group(['prefix' => 'docs'], function() {

    Route::get('boletas-honorarios', 'HonoraryTicketController@index')->middleware('auth.basic');
    Route::post('boletas-honorarios', 'HonoraryTicketController@store')->middleware('auth.basic');
    Route::get('boletas-honorarios/{id_solicitud}', 'HonoraryTicketController@show')->middleware('auth.basic');
    Route::patch('boletas-honorarios/{id_solicitud}', 'HonoraryTicketController@update')->middleware('auth.basic');

});
