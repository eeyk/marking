<?php
Route::get('/','SessionsController@index')->name('index');
Route::group(['middleware'=>'guest'],function(){

    Route::get('login','SessionsController@create')->name('login');
    Route::post('login','SessionsController@store')->name('login');

});
Route::get('logout','SessionsController@destroy')->name('logout')->middleware('auth');
Route::get('getIdentityAction','SessionsController@identity')->middleware('auth');
Route::get('test/{id}','AdminController@isTotallyMarked');

Route::group(['middleware'=>'auth'],function(){



    //Route::post('resetPassword/{id}','UsersController@resetPassword')->name('resetPassword');
    //Route::get('rankall/{id}','UsersController@rankAll')->name('rankAll');
    //Route::get('groupRank/{id}/{group}','UsersController@groupRank')->name('groupRank');

    Route::get('activityOfUser/{id}','UsersController@activityOfUser');
    Route::post('searchPlayer','UsersController@searchPlayer');
    Route::post('/getAllPlayers','UsersController@getAllPlayers');
    Route::get('/playerOfUser/{id}','UsersController@playerDetail');
    Route::post('marking','UsersController@postScore');


});

Route::group(['middleware'=>'admin'],function(){

    Route::post('create/activity','AdminController@postCreateActivity');
    Route::get('getActivityList/{listType}','AdminController@getActivityList');
    Route::get('activity/{id}','AdminController@showActivity');
    Route::post('update/activity','AdminController@updateActivity');
    Route::post('finishActivity','AdminController@destroy');
    Route::post('restoreActivity','AdminController@restore');
    Route::post('/deleteActivity/{id}','AdminController@deleteActivity');
    Route::post('/searchActivity','AdminController@searchActivity');

    Route::post('/getUserTable','AdminController@getUserTable');
    Route::get('user/{id}','AdminController@getUser');
    Route::post('update/user','AdminController@updateUser');

    Route::post('/getPlayerTable','AdminController@getPlayerTable');
    Route::get('player/{id}','AdminController@getPlayer');
    Route::post('update/player','AdminController@updatePlayer');
    Route::get('player/detail/{id}','AdminController@playerDetail');
});
