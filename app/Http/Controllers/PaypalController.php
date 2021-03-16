<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
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
        $paypalConfig = Config::get('paypal');

        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['client_id'],
                $paypalConfig['client_secret']
            )
        );

        $this->apiContext->setConfig($paypalConfig['settings']);
    }

    public function successUrl() {
        return 'el usuario ha pagado';
    }

    public function cancelUrl() {
        return 'el usuario ha cancelado el pago';
    }

    public function createPaymount()
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal(3.99)
            ->setCurrency('USD'); //tipo de moneda

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription('My first Payment with paypal')
            ->setInvoiceNumber(uniqid()); //numero de factura

        $url1 = url('/message-success');
        $url2 = url('/message-cancel');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($url1) //nos dice si el usuario si ha pagado o no
            ->setCancelUrl($url2);

        $payment = new Payment();
        $payment->setIntent('sale') //setiar la intencion establecida en 'venta'
            ->setPayer($payer) //metodo de pago
            ->setTransactions(array($transaction)) //transacciones
            ->setRedirectUrls($redirectUrls); //redirecciones

        try {
            $payment->create($this->apiContext);
        } catch (PayPalConnectionException $ex) {
            $ex->getData();
        }
        return $payment;

    }

    public function paypalCheckout(Request $request)
    {
        $paymentId = $request->paymentID;
        $payerId = $request->payerID;

        if (!$paymentId || !$payerId) {
            return 'Lo sentimos, el pago con paypal no se pudo realizar';
        }

        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);

            if ($result->getState() === 'approved') {
                return 'Gracias, el pago con paypal ha sido exitoso!';
            }

            return 'Lo sentimos, el pago con paypal no se pudo realizar';
        } catch (PayPalConnectionException $ex) {
            return $ex->getData();
        }

        return $result;
    }
}
