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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(
    [
        'prefix' => 'user',
        'namespace' => 'User'
    ],
    function(){
        Route::post('register','AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post("contact/add","ContactController@add_contact");
        Route::get("contact/get-all/{token}/{pagination?}","ContactController@get_paginated_data");
        Route::post("contact/update/{id}","ContactController@edit_single_data");
        Route::get("contact/delete/{id}","ContactController@delete_contact");
        Route::get("contact/get-contact/{id}","ContactController@get_single_data");
        Route::get("contact/search/{search}/{token}/{pagination?}","ContactController@search_data");
    }
);
