<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
// use PayPal\Exception\PayPalConnectionException;

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

    public function createPaymount()
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(3)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(2.30);

        $itemList = new ItemList();
        $itemList->setItems(array($item));

        $details = new Details();
        $details->setShipping(2.2)
            ->setTax(1.3)
            ->setSubtotal(6.9); //total de items

        $amount = new Amount();
        $amount->setTotal(10.4)
            ->setCurrency('USD') //tipo de moneda
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('My first Payment with paypal')
            ->setInvoiceNumber(uniqid()); //numero de factura

        $url = url('/paypal/execute-payment');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($url) //nos dice si el usuario si ha pagado o no
            ->setCancelUrl($url);

        $payment = new Payment();
        $payment->setIntent('Sale') //setiar la intencion establecida en 'venta'
            ->setPayer($payer) //metodo de pago
            ->setRedirectUrls($redirectUrls) //redirecciones
            ->setTransactions(array($transaction)); //transacciones

        try {
            $payment->create($this->apiContext);
        } catch (Exception $ex) {
            echo $ex;
            exit(1);
        }

        return $payment;
    }

    public function paypalCheckout(Request $request)
    {
        $paymentId = $request->paymentId;
        $payerId = $request->PayerID;
        $token = $request->token;

        if (!$paymentId || !$payerId || !$token) {
            return 'Lo sentimos, el pago con paypal no se pudo realizar';
        }

        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setShipping(2.2) //establece envio
            ->setTax(1.3) //establece impuestos
            ->setSubtotal(6.9);

        $amount->setCurrency('USD');
        $amount->setTotal(10.4);
        $amount->setDetails($details);
        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        try {
            $result = $payment->execute($execution, $this->apiContext);

            if ($result->getState() === 'approved') {
                return $result;
            } else {
                return 'Lo sentimos, el pago con paypal no se pudo realizar';
            }
        } catch (Exception $ex) {
            echo $ex;
            exit(1);
        }
    }
}
