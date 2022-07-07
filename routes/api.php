<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

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

Route::group(['middleware' => ['api','throttle:60,1']], function ($router) {
    Route::post('login',  [AuthController::class ,'login']);
    Route::post('logout', [AuthController::class ,'logout']);
    Route::post('refresh',[AuthController::class ,'refresh']);
    Route::post('register', [AuthController::class ,'register']);
    //Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('post/{id}', [PostController::class, 'show']);
    Route::get('get-like/{id}', [PostController::class, 'get_likes']);
    Route::post('like', [PostController::class, 'likeOnPost']);
    Route::post('unlike', [PostController::class, 'unLikeOnPost']);
    Route::post('delete', [PostController::class, 'destroy']);
});