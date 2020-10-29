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
