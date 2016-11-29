<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/awss3upload', 'DashboardController@index');
Route::get('/transcode', 'DashboardController@transcode');
Route::get('/upload', 'DashboardController@upload');
Route::post('/saveupload','DashboardController@showUploadFile');
Route::post('/thumbnail', 'DashboardController@getThumbnail');
Route::get('/welcome', 'DashboardController@welcome');
Route::post('/uploadimage','DashboardController@uploadFileToS3');
Route::get('/uploadimage', 'DashboardController@uploadImage');