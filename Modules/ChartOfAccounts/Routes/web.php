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


Route::middleware(['web',  'SetSessionData',  'language', 'timezone', 'AdminSidebarMenu'])->prefix('chartofaccounts')->group(function() {
    // Route::get('/', 'ChartOfAccountsController@index');
    Route::get('/home', 'ChartOfAccountsController@index');
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');


    Route::get('/chart_view','ChartOfAccountsController@chart_view'); // default route for module
    Route::get('/addaccount','ChartOfAccountsController@addacount');
    Route::post('/addaccount','ChartOfAccountsController@saveacount');
    Route::get('/getaccount','ChartOfAccountsController@getaccount');

});
