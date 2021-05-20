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

//user(view profile)
Route::post('/registro','UserController@registro');
Route::get('/my-profile/{id}', 'UserController@profile');
Route::put('/update-user/{id}','UserController@update');
Route::post('/upload-avatar','UserController@uploadFile');
Route::get('/my-avatar/{image}', 'UserController@getFile');

//other views
Route::get('/preguntas', 'CustomController@getPreguntas');
Route::get('/condiciones', 'CustomController@getCondiciones');
Route::post('/comunicate', 'CustomController@saveComunicate');
Route::get('/nosotros', 'CustomController@getNosotros');
Route::get('/protagonistas', 'CustomController@getProtagonistas');
Route::get('/testimonios', 'CustomController@getTestimonios');



//pasarela de pago
Route::get('/paymount/{amount}/{stripeToken}', 'StripeController@methodPay');
Route::get('/paypal/create-payment/{amount}', 'PaypalController@createPaymount');
Route::get('/paypal/execute-payment', 'PaypalController@paypalCheckout');

//auth google y  facebook
Route::post('/auth-verify-google', 'GoogleController@authLoginWithGoogle');
Route::get('/auth-facebook', 'FacebookController@authLoginFacebook');


//auth with jwtw
Route::post('/sign-up', 'UserController@signUp');
Route::post('/sign-in', 'UserController@signIn');

//orden
Route::post('/orden', 'OrdenController@saveOrdenByUser');

//tasa de cambio
Route::get('/tasa-cambio', 'TasaCambioController@getTasaCambioMonedas');