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
            'amount' => 100,
            'currency' => 'usd',
            'description' => 'my Paymount charge',
            'source' => $token,
        ]);

        return $charge;
    }
}
