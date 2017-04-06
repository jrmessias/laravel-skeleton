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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('login/social/{provider}', ['uses' => 'Auth\LoginController@redirectToProvider', 'as' => 'login.social']);
Route::get('login/social/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::resource('nerds', 'NerdController');
