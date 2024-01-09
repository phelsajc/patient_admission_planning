<?php

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('addEmployee', 'AuthController@refresh');
    
});



Route::match(['get','post'],'getSDtations','CensusController@getSDtations');
Route::match(['get','post'],'getCensus','CensusController@index');
Route::match(['get','post'],'er_list','CensusController@getErList');
Route::match(['get','post'],'getEeReport','CensusController@getEeReport');
Route::match(['get','post'],'store-acuity','CensusController@storeAcuityInfo');
Route::match(['get','post'],'patients-find','CensusController@patientsFind');


Route::match(['get','post'],'addusers','UserController@registerUser');
Route::match(['get','post'],'listusers','UserController@getAllUsers');
Route::match(['get','post'],'getUser/{id}','UserController@getUser');
















