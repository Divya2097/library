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
Route::namespace('api')->group(function () {
	Route::post('/register',						'UserController@register');
	Route::post('/login',							'UserController@login');
	Route::post('/view_books',						'UserController@view_books');

	Route::group([ 'middleware' => 'auth.api'], function()
	{
		Route::post('/subscribe_book',				'UserController@subscribe_book');
	});
	
});
