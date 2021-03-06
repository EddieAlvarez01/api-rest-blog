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

Route::get('/', function () {
    return view('welcome');
});

//RUTAS DE USUARIOS
Route::group(['prefix' => 'user'], function (){
    Route::post('create', 'UserController@register')->withoutMiddleware('jwt-auth');
    Route::post('login', 'UserController@login')->withoutMiddleware('jwt-auth');
    Route::put('update', 'UserController@update');
    Route::get('get-image/{filename?}', 'UserController@getImage')->withoutMiddleware('jwt-auth');
    Route::get('get-user/{id}', 'UserController@getUser')->where([
        'id' => '[0-9]+'
    ])->withoutMiddleware('jwt-auth');
});

//RUTAS DE LAS CATEGORIAS POR RESOURCE
Route::resource('/category', 'CategoryController');

//RUTAS DE LOS POTS POR RESOURCE Y NORMALES
Route::resource('/post', 'PostController');
Route::get('/post-resources/get-image/{file?}', 'PostController@getImage');
Route::get('/post-resources/get-post-category/{id}', 'PostController@getPostByCategory');
Route::get('/post-resources/get-post-user/{id}', 'PostController@getPostByUser');
