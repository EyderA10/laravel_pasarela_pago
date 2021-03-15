<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends Controller
{
    public function methodPay(Request $request)
    {

        Stripe::setApiKey(config('services.stripe.secret'));

        $token = $request->stripeToken;

        $charge = Charge::create([
            'amount' => 10,
            'currency' => 'usd',
            'description' => 'my Paymount charge',
            'source' => $token,
        ]);

        $data = [
            'status' => 'success',
            'code' => 200,
            'paymount' => $charge
        ];

        return response()->json($data, $data['code']);
    }
}
