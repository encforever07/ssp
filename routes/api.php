<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::group(['middleware' => 'IsAdmin'], function () {
    Route::resource('usuarios', 'UserController');

    Route::resource('especialidades', 'EspecialidadController');



    Route::get('/search/usuarios/{field}/{value}', 'UserController@search');

    Route::get('/search/especialidades/{field}/{value}', 'EspecialidadController@search');

    Route::get('/roles', function () {
        return \App\models\Rol::all();
    });

    Route::get('/cat_pagos', function () {
        return \App\models\CatPago::all();
    });
});*/
//Route::post('login', 'Auth\LoginController@login');
//Route::post('logout', 'Auth\LoginController@logout')->name('logout');






