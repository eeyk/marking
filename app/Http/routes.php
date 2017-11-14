<?php
Route::get('/','SessionsController@index')->name('index');
Route::group(['middleware'=>'guest'],function(){

    Route::get('login','SessionsController@create')->name('login');
    Route::post('login','SessionsController@store')->name('login');

});
Route::get('logout','SessionsController@destroy')->name('logout')->middleware('auth');

Route::group(['middleware'=>'auth'],function(){

    Route::get('/getAllPlayers','UsersController@getAllPlayers')->name('getAllPlayers');
    Route::get('player/{id}','UsersController@show')->name('playerDetail');
    Route::post('marking/{id}','UsersController@postScore')->name('marking');
//    Route::get('ranking/{id}','UsersController@rank')->name('rank');
    Route::get('rankall/{id}','UsersController@rankAll')->name('rankAll');
    Route::get('groupRank/{id}/{group}','UsersController@groupRank')->name('groupRank');

});

Route::group(['middleware'=>'admin'],function(){

    Route::get('activity/{id}','AdminController@showActivity')->name('showActivity');
    Route::get('create/activity','AdminController@getCreateActivity')->name('createActivity');
    Route::post('create/activity','AdminController@postCreateActivity')->name('createActivity');
    Route::post('createPlayer','AdminController@createPlayer')->name('createPlayer');
    Route::post('createUser','AdminController@createUser')->name('createUser');
    Route::get('update/activity/{id}','AdminController@getUpdateActivity')->name('updateActivity');
    Route::get('update/user/{id}','AdminController@getUpdateUser')->name('updateUser');
    Route::get('update/player/{id}','AdminController@getUpdatePlayer')->name('updatePlayer');
    Route::post('update/activity/{id}','AdminController@updateActivity')->name('updateActivity');
    Route::post('update/user/{id}','AdminController@updateUser')->name('updateUser');
    Route::post('update/player/{id}','AdminController@updatePlayer')->name('updatePlayer');
    Route::delete('delete/{id}','AdminController@destroy')->name('deleteActivity');
    Route::get('restore/{id}','AdminController@restore')->name('restoreActivity');
    Route::get('admin','AdminController@admin')->name('admin');
    Route::get('marked/player/{id}','AdminController@markedPlayer')->name('markedPlayer');
    Route::get('unMarked/player/{id}','AdminController@unMarkedPlayer')->name('unMarkedPlayer');
    Route::get('marked/player/detail/{id}','AdminController@markedPlayerDetail')->name('markedPlayerDetail');
    Route::get('unMarked/player/detail/{id}','AdminController@unMarkedPlayerDetail')->name('unMarkedPlayerDetail');

});
