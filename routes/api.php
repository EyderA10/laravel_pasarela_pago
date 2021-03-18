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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//pasarela de pago
Route::get('/paymount/{stripeToken}', 'StripeController@methodPay');
Route::get('/paypal/create-payment', 'PaypalController@createPaymount');
Route::get('/paypal/execute-payment', 'PaypalController@paypalCheckout');

//socialite with google and facebook
Route::get('/login/{service}/redirect','SocialiteController@redirectToProvider');
Route::get('/login/{service}/callback','SocialiteController@handleProviderCallback');

//auth with jwtw
Route::post('/sign-up', 'UserController@signUp');
Route::post('/sign-in', 'UserController@signIn');
