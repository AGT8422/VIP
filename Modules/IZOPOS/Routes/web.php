<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['setData',   'SetSessionData',  'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->prefix('izopos')->group(function() {
    Route::get('/', 'InstallmentController@index');
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');

    
    Route::get('/', 'IZOPOSController@index');
    Route::get('/home', 'IZOPOSController@index');
    Route::get('/close-box', 'IZOPOSController@closeBox');
    Route::get('/bills', 'IZOPOSController@Bills');
    Route::get('/pos', 'IZOPOSController@POS');
    Route::get('/sales-report', 'IZOPOSController@salesReport');
});
