<?php

use Illuminate\Support\Facades\Route;

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

Route::redirect('/', '/home');

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/pocket/store', 'HomeController@store')->name('pocket.store');
    Route::get('/pocket/page', 'HomeController@page')->name('pocket.page');
    Route::get('/pocket/chart', 'HomeController@chart')->name('pocket.chart');
    Route::post('/pocket/update', 'HomeController@update')->name('pocket.update');
});
