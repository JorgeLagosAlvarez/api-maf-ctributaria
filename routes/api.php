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

    Route::get('boletas-honorarios', 'HonoraryTicketController@index')->name('honorarytickets.index')->middleware('auth.basic');
    Route::post('boletas-honorarios', 'HonoraryTicketController@store')->name('honorarytickets.store')->middleware('auth.basic');
    Route::get('boletas-honorarios/{id_solicitud}', 'HonoraryTicketController@show')->name('honorarytickets.show')->middleware('auth.basic');
    Route::patch('boletas-honorarios/{id_solicitud}/{workitemid}', 'HonoraryTicketController@update')->name('honorarytickets.update')->middleware('auth.basic');

    Route::get('cavs', 'CavController@index')->name('cavs.index')->middleware('auth.basic');
    Route::post('cavs', 'CavController@store')->name('cavs.store')->middleware('auth.basic');
    Route::get('cavs/{id_solicitud}', 'CavController@show')->name('cavs.show')->middleware('auth.basic');
    Route::patch('cavs/{id_solicitud}/{workitemid}', 'CavController@update')->name('cavs.update')->middleware('auth.basic');
  
    Route::get('cupos-taxis', 'CupoTaxiController@index')->name('cupostaxis.index')->middleware('auth.basic');
    Route::post('cupos-taxis', 'CupoTaxiController@store')->name('cupostaxis.store')->middleware('auth.basic');
    Route::get('cupos-taxis/{id_solicitud}', 'CupoTaxiController@show')->name('cupostaxis.show')->middleware('auth.basic');
    Route::patch('cupos-taxis/{id_solicitud}/{workitemid}', 'CupoTaxiController@update')->name('cupostaxis.update')->middleware('auth.basic');
 
    Route::get('situacion-tributarias', 'SituacionTributariaController@index')->name('situaciontributarias.index')->middleware('auth.basic');
    Route::post('situacion-tributarias', 'SituacionTributariaController@store')->name('situaciontributarias.store')->middleware('auth.basic');
    Route::get('situacion-tributarias/{id_solicitud}', 'SituacionTributariaController@show')->name('situaciontributarias.show')->middleware('auth.basic');
    Route::patch('situacion-tributarias/{id_solicitud}/{workitemid}', 'SituacionTributariaController@update')->name('situaciontributarias.update')->middleware('auth.basic');

    Route::get('certificados-afps', 'AfpDocumentController@index')->name('certificadosafps.index')->middleware('auth.basic');
    Route::post('certificados-afps', 'AfpDocumentController@store')->name('certificadosafps.store')->middleware('auth.basic');
    Route::get('certificados-afps/{id_solicitud}', 'AfpDocumentController@show')->name('certificadosafps.show')->middleware('auth.basic');
    Route::patch('certificados-afps/{id_solicitud}/{workitemid}', 'AfpDocumentController@update')->name('certificadosafps.update')->middleware('auth.basic');

    Route::get('liquidacion-carabineros', 'LiquidacionCarabineroController@index')->name('liquidacioncarabinero.index')->middleware('auth.basic');
    Route::post('liquidacion-carabineros', 'LiquidacionCarabineroController@store')->name('liquidacioncarabinero.store')->middleware('auth.basic');
    Route::get('liquidacion-carabineros/{id_solicitud}', 'LiquidacionCarabineroController@show')->name('liquidacioncarabinero.show')->middleware('auth.basic');
    Route::patch('liquidacion-carabineros/{id_solicitud}/{workitemid}', 'LiquidacionCarabineroController@update')->name('liquidacioncarabinero.update')->middleware('auth.basic');
    Route::get('b64/liquidacion-carabineros/{file_name}', 'LiquidacionCarabineroController@showb64')->name('liquidacioncarabinero.showb64')->middleware('auth.basic');
    
});
