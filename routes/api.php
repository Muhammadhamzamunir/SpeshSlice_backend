<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\registerController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\HandleBakeryController;
use App\Http\Middleware\tokenAuthentication;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PaymentController;


Route::post('/update-user/{id}', [loginController::class, "updateUser"])->middleware(tokenAuthentication::class);
Route::post('/update-userPassword/{id}', [loginController::class, "changePassword"])->middleware(tokenAuthentication::class);


//Users Route
Route::post('/logout', [loginController::class,"logout"]);
Route::post('/login', [loginController::class,"login"]);
Route::post('/signup',[registerController::class,"registrationHandle"])->name('registeration');

// Address Route
Route::get('/address/{id?}', [AddressController::class, "getAddresses"]);
Route::post('/address/{id?}', [AddressController::class, "saveAddresses"]);
Route::post('/userAddress/{id?}', [AddressController::class, "saveUserAddresses"]);
Route::delete('/userAddress/{id?}', [AddressController::class, "deleteUserAddresses"]);
Route::post('/setUserDefaultAddress/{id}',[AddressController::class, "setDefaultAddress"]);

// Bakery Routes
Route::get('/bakery/{id?}', [HandleBakeryController::class, "getBakeries"]);
Route::post('/register-bakery', [HandleBakeryController::class, "registerBakeryhandle"])->middleware(tokenAuthentication::class);
Route::post('/update-bakery/{id}', [HandleBakeryController::class, "updateBakery"])->middleware(tokenAuthentication::class);
Route::delete('/delete-bakery/{id}', [HandleBakeryController::class, "deleteBakery"])->middleware(tokenAuthentication::class);
Route::get('/bakeries-near-user/{id}', [HandleBakeryController::class, "getBakeriesNearuser"]);

//Category Routes
Route::get('/category',[CategoryController::class,"getCategories"]);
Route::post('/category',[CategoryController::class,"registerCategory"]);

//Product Routes
Route::get('/products/{productId?}', [ProductController::class, 'getProduct']);
Route::get('/products/bakery/{bakeryId}', [ProductController::class, 'getProductsByBakery']);
Route::get('/products/category/{categoryName}', [ProductController::class, 'getProductsByCategory']);
Route::post('/products', [ProductController::class, 'addProduct'])->middleware(tokenAuthentication::class);
Route::delete('/products/{id}', [ProductController::class, 'deleteProduct'])->middleware(tokenAuthentication::class);
Route::post('/update-products/{id}', [ProductController::class, 'updateProduct'])->middleware(tokenAuthentication::class);

//Cart Routes
Route::get('/cart/{user}', [CartController::class, 'getCartProducts']);
Route::post('/cart/{user_id}/{product_id}/{quantity}', [CartController::class, 'addCartProducts']);
Route::delete('/cart/{id}', [CartController::class, 'deleteProductFromCart']);

//product review Route
Route::get('/pending-feedback/{userId}' ,[FeedbackController::class ,'getPendingFeedback']);
Route::delete('/pending-feedback/{userId}' ,[FeedbackController::class ,'deletePendingFeedback']);
Route::post('/bakery/{bakeryId}/review', [HandleBakeryController::class, 'addBakeryReview']);
Route::post('/products/{productId}/review', [ProductController::class, 'addReview']);

//Discounted Products
Route::get('/discounts', [ProductController::class, 'getProductsWithDiscount']);






Route::post('/process-payment', [PaymentController::class, 'processPayment']);
Route::post('/create-payment-intent', 'PaymentController@createPaymentIntent');