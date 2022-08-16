<?php

use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'auth', 'controller' => \App\Http\Controllers\Dashboard\AuthController::class], function () {
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/sign-in', 'signIn')->name('dashboard.auth.sign-in');
   });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/sign-out', 'signOut')->name('dashboard.auth.sign-out');
    });


});

Route::group(['middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\AuthController::class], function () {
    Route::get('/me', 'me')->name('dashboard.me');
});


