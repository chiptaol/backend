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


Route::group(['prefix' => 'cinemas', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\CinemaController::class], function () {
    Route::get('/', 'index')->name('dashboard.cinemas.index');
    Route::get('/{id}', 'show')->name('dashboard.cinemas.show');
    Route::post('/', 'store')->name('dashboard.cinemas.store');
    Route::post('/logo', 'storeLogo')->name('dashboard.cinemas.store-logo');
    Route::put('/{id}', 'update')->name('dashboard.cinemas.update');
    Route::delete('/{id}', 'delete')->name('dashboard.cinemas.delete');
});

Route::group(['prefix' => 'cinemas', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\HallController::class], function () {
    Route::get('/{cinemaId}/halls', 'index')->name('dashboard.halls.index');
    Route::get('/{cinemaId}/halls/{id}', 'show')->name('dashboard.halls.show');
    Route::post('/{cinemaId}/halls', 'store')->name('dashboard.halls.store');
    Route::put('/{cinemaId}/halls/{id}', 'update')->name('dashboard.halls.update');
    Route::delete('/{cinemaId}/halls/{id}', 'delete')->name('dashboard.halls.delete');
});

Route::group(['prefix' => 'cinemas', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\SeatController::class], function () {
    Route::get('/{cinemaId}/halls/{hallId}/seats', 'index')->name('dashboard.seats.index');
    Route::post('/{cinemaId}/halls/{hallId}/seats', 'store')->name('dashboard.seats.store');
    Route::delete('/{cinemaId}/halls/{hallId}/seats', 'delete')->name('dashboard.seats.delete');
});

Route::group(['prefix' => 'formats', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\FormatController::class], function () {
    Route::get('/', 'index')->name('dashboard.formats.index');
});

Route::group(['prefix' => 'cinemas', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\SeanceController::class], function () {
    Route::get('{cinemaId}/seances', 'index')->name('dashboard.seances.index');
    Route::post('/{cinemaId}/seances', 'store')->name('dashboard.seances.store');
    Route::get('/{cinemaId}/seances/{seanceId}', 'show')->name('dashboard.seances.show');
    Route::delete('/{cinemaId}/seances/{seanceId}', 'delete')->name('dashboard.seances.delete');
});


Route::group(['prefix' => 'movies', 'middleware' => 'auth:api', 'controller' => \App\Http\Controllers\Dashboard\MovieController::class], function () {
    Route::get('/search/{title}', 'search')->name('dashboard.movies.search');
});
