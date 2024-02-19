<?php

Route::get('/','InicioController@home')->name('home');
Route::post('cargaLayoutVentaSemanal','VentasSemanalController@cargaLayoutVentaSemanal')->name('cargaLayoutVentaSemanal');
Route::get('descagaLayoutSemanal','VentasSemanalController@descagaLayoutSemanal')->name('descagaLayoutSemanal');