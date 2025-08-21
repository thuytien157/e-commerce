<?php

use App\Http\Controllers\Attribute;
use App\Http\Controllers\Category;
use App\Http\Controllers\Order;
use App\Http\Controllers\Product;
use App\Http\Controllers\Review;
use App\Http\Controllers\User;
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


Route::get('/{provide}/redirect', [User::class, 'redirect']);
Route::get('/{provide}/callback', [User::class, 'callback']);
Route::get('/logout', [User::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [User::class, 'register']);
Route::post('/login', [User::class, 'login']);
Route::post('/sendcode', [User::class, 'sendCode']);
Route::post('/verify-code', [User::class, 'verifyResetCode']);
Route::post('/reset-password', [User::class, 'resetPassword']);
Route::get('/user', [User::class, 'getUserById'])->middleware('auth:sanctum');
Route::post('/insert-address', [User::class, 'insertAddress'])->middleware('auth:sanctum');
Route::put('/edit-address/{address_id}', [User::class, 'editAddress'])->middleware('auth:sanctum');
Route::get('/addresses/{address_id}', [User::class, 'getAddressById'])->middleware('auth:sanctum');
Route::post('/update-avatar', [User::class, 'updateAvatar'])->middleware('auth:sanctum');
Route::get('/order-history-user', [User::class, 'getOrderByUser'])->middleware('auth:sanctum');
Route::get('/user-info', [User::class, 'getAllUser'])->middleware('auth:sanctum');
Route::put('/assign-role/{id}', [User::class, 'assignRole'])->middleware('auth:sanctum');
Route::put('/lock/{id}', [User::class, 'lockUser'])->middleware('auth:sanctum');


Route::get('/products_detail/{id}', [Product::class, 'getProductById']);
Route::put('/product/hidden/{id}', [Product::class, 'hiddenProduct']);
Route::resource('product', Product::class);

Route::resource('category', Category::class);

Route::delete('/attribute-value/{id}', [Attribute::class, 'deleteAttributeValue']);
Route::resource('attribute', Attribute::class);


Route::post('/ghn/service', [Order::class, 'getGHNServices']);
Route::post('/order', [Order::class, 'createOrder']);
Route::get('/order', [Order::class, 'getAllOrders']);
Route::get('/order-info/{id}', [Order::class, 'getOrderById']);
Route::put('/cancel-order', [Order::class, 'cancelOrder']);
Route::put('/update-order-address', [Order::class, 'updateAddresslOrder']);
Route::match(['GET', 'POST'], '/vnpay-ipn', [Order::class, 'handleVnpayIpn']);
Route::get('/payment-return', [Order::class, 'paymentOrderReturn']);
Route::post('/order/{orderId}/repay', [Order::class, 'repayOrder']);
Route::put('/order-update-status/{id}', [Order::class, 'updateStatus']);
Route::get('/invoice/{id}', [Order::class, 'generateInvoice']);


Route::post('/review', [Review::class, 'createReview'])->middleware('auth:sanctum');
Route::get('/review', [Review::class, 'getAllReview']);
Route::put('/review/hide/{id}', [Review::class, 'hiddenReview']);
Route::put('/review/reply/{id}', [Review::class, 'replyReview'])->middleware('auth:sanctum');
