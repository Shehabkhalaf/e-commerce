<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*-------Admin module-------*/
/*Categories resource*/
Route::resource('categories', CategoryController::class);
/*Promo codes resource*/
Route::resource('promocodes', PromoCodeController::class);
/*Products resource*/
Route::resource('products', ProductController::class);
/*Orders*/
Route::get('orders',[AdminOrderController::class,'orders']);
Route::get('orders/{id}',[AdminOrderController::class,'orderDetails']);
Route::get('customers',[AdminOrderController::class,'allCustomers']);

/*-------User module-------*/
Route::prefix('user')->group(function (){
    /*-------Product module-------*/
   Route::get('products',[\App\Http\Controllers\User\ProductController::class,'allProducts']);
   Route::get('products/{id}',[\App\Http\Controllers\User\ProductController::class,'showProduct']);
   Route::get('categories',[CategoryController::class,'index']);
   /*-------Message module-------*/
   Route::post('contact_us',[\App\Http\Controllers\User\MessageController::class,'sendMessage']);
   /*-------Promo codes-------*/
    Route::get('promocodes',[PromoCodeController::class,'index']);
    /*-------Order module -------*/
    Route::post('make_order',[\App\Http\Controllers\User\OrderController::class,'makeOrder']);
});
