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
// Hyundai 
Route::group(['middleware' => ['api','throttle:60,1']], function ($router) {
    Route::post('login',  [AuthController::class ,'login']);
    Route::post('logout', [AuthController::class ,'logout']);
    Route::post('refresh',[AuthController::class ,'refresh']);
    Route::post('register', [AuthController::class ,'register']);
    //Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('post/{id}', [PostController::class, 'show']);
    Route::get('posts', [PostController::class, 'index']);
});

// Route::resource('posts', PostController::class);

// // Default
// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login');
//     Route::post('login',  [AuthController::class ,'login']);
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');

// });

// Check Auth Hyundai
// Route::group(['prefix' => 'v1','middleware' => ['jwt.verify','throttle:60,1']], function () {
//     Route::get('/order-list',[OrderController::class,'userOrder']);
//     Route::post('place-order/' , [OrderController::class,'placeOrder']);
//     Route::get('show-order/{id}' , [OrderController::class,'orderDetails']);
//     Route::post('order-payment' , [PaymentController::class,'orderPayment']);

// });

// Public Accessible
// Route::group(['prefix' => 'v1','middleware' => ['throttle:60,1']], function () {
//     Route::get('categories' , [CategoryController::class,'index']);
//     Route::get('products' , [ProductController::class,'index']);
//     Route::get('product/{slug}' , [ProductController::class,'productDescription']);
//     Route::get('home' , [HomeController::class,'index']);

// });