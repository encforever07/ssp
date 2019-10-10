<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'IsAdmin'], function () {
    //views
    Route::get('/usuario', function () {
        return view('admin_users');
    })->name('usuario');
    Route::get('/especialidad', function () {
        return view('especialidades');
    })->name('especialidad');

    //Services
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
});



