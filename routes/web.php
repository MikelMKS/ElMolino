<?php

Route::get('/','InicioController@home')->name('home');

Route::get('productos','ProductosController@index')->name('productos');
Route::get('agregarProducto','ProductosController@agregarProducto')->name('agregarProducto');
Route::post('guardarProducto','ProductosController@guardarProducto')->name('guardarProducto');
Route::get('verProducto','ProductosController@verProducto')->name('verProducto');
Route::get('operacionesProducto','ProductosController@operacionesProducto')->name('operacionesProducto');
Route::post('updateProducto','ProductosController@updateProducto')->name('updateProducto');

Route::post('cargaLayoutVentaSemanal','VentasSemanalController@cargaLayoutVentaSemanal')->name('cargaLayoutVentaSemanal');
Route::get('descagaLayoutSemanal','VentasSemanalController@descagaLayoutSemanal')->name('descagaLayoutSemanal');