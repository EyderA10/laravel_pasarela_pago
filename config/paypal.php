<?php

return [
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_SECRET'),

    'settings' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'log.LogEnabled' => true,
        //en el caso de que ocurran errores los guarde en este path
        'log.FileName' => storage_path('/logs/paypal.log'),
        //nivel del log que queremos
        'log.LogLevel' => 'ERROR'
    ]
];
