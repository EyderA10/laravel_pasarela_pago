<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;

class PaypalController extends Controller
{

    private $apiContext;

    public function __construct()
    {
        $paypalConfig = config('paypal');

        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['client_id'],
                $paypalConfig['client_secret']
            )
        );

        $this->apiContext->setConfig($paypalConfig['settings']);
    }

    public function createPaymount()
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal(2.30)
            ->setCurrency('USD'); //tipo de moneda

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription('My first Payment with paypal');
            // ->setInvoiceNumber(uniqid()); //numero de factura

        $url = url('/paypal/checkout');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($url) //nos dice si el usuario si ha pagado o no
            ->setCancelUrl($url);

        $payment = new Payment();
        $payment->setIntent('sale') //setiar la intencion establecida en 'venta'
            ->setPayer($payer) //metodo de pago
            ->setTransactions(array($transaction)) //transacciones
            ->setRedirectUrls($redirectUrls); //redirecciones

        try {
            $payment->create($this->apiContext);

            $data = [
                'status' => 'success',
                'code' => 200,
                'payment' => $payment
            ];

        } catch (PayPalConnectionException $ex) {
            $data = [
                'status' => 'error',
                'code' => 500,
                'paypal_ex' => $ex->getData()
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function paypalCheckout(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerId');
        $token = $request->input('token');

        if (!$paymentId || !$payerId || !$token) {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Lo sentimos, el pago con paypal no se pudo realizar'
            ];
        }

        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);

            if ($result->getState() === 'approved') {
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Gracias, el pago con paypal ha sido exitoso!',
                    'result' => $result
                ];
            }

            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Lo sentimos, el pago con paypal no se pudo realizar'
            ];
        } catch (PayPalConnectionException $ex) {
            $data = [
                'status' => 'error',
                'code' => 500,
                'paypal_ex' => $ex->getData()
            ];
        }

        return response()->json($data, $data['code']);
    }
}
