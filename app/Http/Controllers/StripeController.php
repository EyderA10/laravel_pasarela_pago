<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends Controller
{
    public function methodPay(Request $request)
    {

        Stripe::setApiKey(config('services.stripe.secret'));

        $token = $request->stripeToken;
        $amount = $request->amount;

        $charge = Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'description' => 'my Paymount charge',
            'source' => $token,
        ]);

        return $charge;
    }
}
