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

Route::get('/example-responses/{name}', \App\Http\Controllers\ApiController::class);


Route::group(['prefix' => 'premieres', 'controller' => \App\Http\Controllers\PremiereController::class], function () {
    Route::get('/', 'index')->name('premieres.index');
    Route::get('/actual', 'indexActual')->name('premieres.index.actual');
    Route::get('/schedule', 'schedule')->name('premieres.schedule');
    Route::get('/{movieId}', 'show')->name('premieres.show');
});
