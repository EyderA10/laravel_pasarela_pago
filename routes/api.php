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
Route::get('/paymount/{amount}/{stripeToken}', 'StripeController@methodPay');
Route::get('/paypal/create-payment/{amount}', 'PaypalController@createPaymount');
Route::get('/paypal/execute-payment', 'PaypalController@paypalCheckout');


Route::post('/get-user-social','SocialiteController@returnUserData');

//auth with jwtw
Route::post('/sign-up', 'UserController@signUp');
Route::post('/sign-in', 'UserController@signIn');

//orden
Route::post('/orden', 'OrdenController@saveOrdenByUser');