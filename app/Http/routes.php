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

get('login','SessionsController@create')->name('login')->middleware('auth');
Route::group(['middleware'=>'auth'],function(){

    post('login','SessionsController@store')->name('login');
    delete('logout','SessionsController@destroy')->name('logout');
});
#post('login','SessionsController@store')->name('login');
#delete('logout','SessionsController@destroy')->name('logout');
Route::group(['middleware'=>'auth'],function(){

  get('index','UsersController@getAllPlayers')->name('getAllPlayers');
  get('ranking/{id}','UsersController@rank')->name('rank');
  get('sorting','UsersController@rankAll')->name('rankAll');
  get('player/{id}','UsersController@show')->name('playerDetail');
  post('marking/{id}','UsersController@postScore')->name('marking');

});
// get('index','UsersController@getAllPlayers')->name('getAllPlayers');
// get('ranking/{id}','UsersController@rank')->name('rank');
// get('sorting','UsersController@rankAll')->name('rankAll');
// get('player/{id}','UsersController@show')->name('playerDetail');
// post('marking/{id}','UsersController@postScore')->name('marking');
Route::group(['middleware'=>'admin'],function(){

  get('create/activity','AdminController@getCreateActivity')->name('createActivity');
  get('create/user','AdminController@getCreateUser')->name('createUser');
  get('create/player','AdminController@getCreatePlayer')->name('createPlayer');
  post('create/activity','AdminController@postCreateActivity')->name('createActivity');
  post('create/user/{name}','AdminController@postCreateUser')->name('createUser');
  post('create/player/{name}','')->name('createPlayer');
  get('update/activity/{id}','AdminController@getUpdateActivity')->name('updateActivity');
  get('update/uset/{id}','AdminController@getUpdateUser')->name('updateUser');
  get('update/player/{id}','AdminController@getUpdatePlayer')->name('updatePlayer');
  patch('update/activity/{id}','AdminController@updateActivity')->name('updateActivity');
  patch('update/users/{id}','AdminController@updateUser')->name('updateUser');
  patch('update/players/{id}','AdminController@updatePlayer')->name('updatePlayer');

});
// get('create/activity','AdminController@getCreateActivity')->name('createActivity');
// get('create/user','AdminController@getCreateUser')->name('createUser');
// get('create/player','AdminController@getCreatePlayer')->name('createPlayer');
// post('create/activity','AdminController@postCreateActivity')->name('createActivity');
// post('create/user/{name}','AdminController@postCreateUser')->name('createUser');
// post('create/player/{name}','')->name('createPlayer');
// get('update/activity/{id}','AdminController@getUpdateActivity')->name('updateActivity');
// get('update/uset/{id}','AdminController@getUpdateUser')->name('updateUser');
// get('update/player/{id}','AdminController@getUpdatePlayer')->name('updatePlayer');
// patch('update/activity/{id}','AdminController@updateActivity')->name('updateActivity');
// patch('update/users/{id}','AdminController@updateUser')->name('updateUser');
// patch('update/players/{id}','AdminController@updatePlayer')->name('updatePlayer');
