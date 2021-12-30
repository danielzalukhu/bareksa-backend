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

// Topics
Route::get('topics', 'API\TopicController@index');
Route::post('topics', 'API\TopicController@store');
Route::get('topics/{id}', 'API\TopicController@show');
Route::put('topics/{id}', 'API\TopicController@update');
Route::delete('topics/{id}', 'API\TopicController@destroy');

// News
Route::get('news', 'API\NewsController@index');
Route::post('news', 'API\NewsController@store');
Route::get('news/{id}', 'API\NewsController@show');
Route::put('news/{id}', 'API\NewsController@update');
Route::delete('news/{id}', 'API\NewsController@destroy');

// Tags
Route::get('tags', 'API\TagController@index');
Route::post('tags', 'API\TagController@store');
Route::get('tags/{id}', 'API\TagController@show');
Route::put('tags/{id}', 'API\TagController@update');
Route::delete('tags/{id}', 'API\TagController@destroy');