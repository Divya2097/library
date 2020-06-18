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

Route::namespace('api')->group(function () {
	Route::get('/add_book',						'AdminController@add_book');
	Route::get('/book_count',						'AdminController@book_count');
	Route::get('/sub_count',						'AdminController@sub_count');
	Route::get('/user_book_sub',						'AdminController@user_book_sub');

	Route::group([ 'middleware' => 'auth.api'], function()
	{
		Route::post('/purchase_book',				'SubscriptionPlanController@purchase_book');
	});
	
});
