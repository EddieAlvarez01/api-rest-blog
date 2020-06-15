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
    Route::get('get-image', 'UserController@getImage');
    Route::get('get-user/{id}', 'UserController@getUser')->where([
        'id' => '[0-9]+'
    ])->withoutMiddleware('jwt-auth');
});

//RUTAS DE LAS CATEGORIAS POR RESOURCE
Route::resource('/category', 'CategoryController');

//RUTAS DE LOS POTS POR RESOURCE Y NORMALES
Route::resource('/post', 'PostController');
Route::get('/post/get-image/{file}', 'PostController@getImage');
Route::get('/post/get-post-category/{id}', 'PostController@getPostByCategory');
Route::get('/post/get-post-user/{id}', 'PostController@getPostByUser');
