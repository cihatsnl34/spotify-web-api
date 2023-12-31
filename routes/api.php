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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
});

Route::group([
    'middleware' => ['auth:api']
], function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/myProfile', [\App\Http\Controllers\AuthController::class, 'authenticate']);
    Route::post('/storeProfilePhoto', [\App\Http\Controllers\Api\UserController::class, 'uploadProfilePhoto']);
    Route::post('/changeProfilePhoto', [\App\Http\Controllers\Api\UserController::class, 'updateProfilePhoto']);
    Route::post('/top-artists', [\App\Http\Controllers\Api\SpotifyController::class, 'getTopArtists']);
    Route::post('/artist-all-track', [\App\Http\Controllers\Api\SpotifyController::class, 'getArtistTrack']);
    Route::post('/genres-all-track', [\App\Http\Controllers\Api\SpotifyController::class, 'getGenresTrack']);

});