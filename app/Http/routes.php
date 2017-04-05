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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware'=>'guest'],function(){

  Route::get('login','SessionsController@create')->name('login');
  Route::post('login','SessionsController@store')->name('login');

});
Route::delete('logout','SessionsController@destroy')->name('logout')->middleware('auth');

Route::group(['middleware'=>'auth'],function(){

  Route::get('index','UsersController@getAllPlayers')->name('index');
  Route::get('player/{id}','UsersController@show')->name('playerDetail');
  Route::post('marking/{id}','UsersController@postScore')->name('marking');

});

Route::group(['middleware'=>'admin'],function(){

  Route::get('create/activity','AdminController@getCreateActivity')->name('createActivity');
  Route::get('activity/{id}','AdminController@showActivity')->name('showActivity');
  #Route::get('create/user','AdminController@getCreateUser')->name('createUser');
  Route::get('create/player','AdminController@getCreatePlayer')->name('createPlayer');
  Route::post('create/activity','AdminController@postCreateActivity')->name('createActivity');
  Route::post('create/user/','AdminController@postCreateUser')->name('createUser');
  #Route::post('create/player/{id}','')->name('createPlayer');
  Route::get('update/activity/{id}','AdminController@getUpdateActivity')->name('updateActivity');
  Route::get('update/user/{id}','AdminController@getUpdateUser')->name('updateUser');
  Route::get('update/player/{id}','AdminController@getUpdatePlayer')->name('updatePlayer');
  Route::patch('update/activity/{id}','AdminController@updateActivity')->name('updateActivity');
  Route::patch('update/user/{id}','AdminController@updateUser')->name('updateUser');
  Route::patch('update/player/{id}','AdminController@updatePlayer')->name('updatePlayer');
  Route::get('ranking/{id}','AdminController@rank')->name('rank');
  Route::get('rankall/{id}','AdminController@rankAll')->name('rankAll');
});
Route::get('test/{id}','UsersController@isMarking');
Route::get('testdelete/{id}','AdminController@destroy');
Route::get('testrestore/{id}','AdminController@restore');
