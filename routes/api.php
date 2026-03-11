<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\front\ShippingController as FrontShippingController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/register', [AuthController::class, 'register']);

Route::get('/user', [AuthController::class, 'user']);


// forgot password routes
Route::post('/admin/check-email', [AuthController::class,'checkEmail']);
Route::post('/admin/update-password', [AuthController::class,'updatePassword']);

Route::get('/latest_products', [FrontProductController::class, 'latestProducts']);
Route::get('/feature_products', [FrontProductController::class, 'featureProducts']);
Route::get('/get_categories', [FrontProductController::class, 'getCategories']);
Route::get('/get_brands', [FrontProductController::class, 'getBrands']);
Route::get('/get_products', [FrontProductController::class, 'getProducts']);
Route::get('/get_product/{id}', [FrontProductController::class, 'getProduct']);
Route::post('/register', [AccountController::class, 'register']);
Route::post('/login', [AccountController::class, 'login']);
Route::get('get_front_shipping', [FrontShippingController::class, 'getShipping']);

// forgot password routes
Route::post('/check-email', [AccountController::class,'checkEmail']);
Route::post('/update-password', [AccountController::class,'updatePassword']);

Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function(){
    Route::post('/save_order', [OrderController::class, 'saveOrder']);
    Route::get('/order_details/{id}', [AccountController::class, 'orderDetails']);
    Route::get('/get_orders', [AccountController::class, 'getOrders']);
    Route::post('/update_account', [AccountController::class, 'updateAccount']);
    Route::get('/account_details', [AccountController::class, 'accountDetails']);
});

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function(){
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::get('sizes', [SizeController::class, 'index']);
    Route::resource('products', ProductController::class);
    Route::post('temp-image', [TempImageController::class, 'store']);

    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{id}', [AdminOrderController::class, 'detalis']);
    Route::post('update_order/{id}', [AdminOrderController::class, 'updateOrder']);

    Route::get('get_shipping', [ShippingController::class, 'getShipping']);
    Route::post('save_shipping', [ShippingController::class, 'saveOrUpdate']);
});