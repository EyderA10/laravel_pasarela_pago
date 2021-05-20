<?php

namespace App\Http\Controllers;

use App\Models\TasaCambio;

class TasaCambioController extends Controller
{
    public function getTasaCambioMonedas()
    {
        $cambio = TasaCambio::get();

        if (!empty($cambio) || isset($cambio)) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'response' => $cambio
            ];
            return response()->json($data, $data['code']);
        } else {
            return response()->json(['message' => 'no existen registros'], 500);
        }
    }
}
