<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

class SocialiteController extends Controller
{
    //muestra la pagina de autenticacion con google

    public function redirectToProvider(Request $request)
    {
        $service = $request->service;

        if (!$service) {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'Error el servicio con el que deseas autenticar no existe'
            ];

            return response()->json($data, $data['code']);
        }

        return Socialite::driver($service)->stateless()->redirect();
    }

    public function handleProviderCallback(Request $request)
    {

        $service = $request->service;

        if (!$service) {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'Error el servicio con el que deseas autenticar no existe'
            ];

            return response()->json($data, $data['code']);
        }
        
        try {
            return Socialite::driver($service)->stateless()->user();
            
        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'error' => 'Error al intentar iniciar sesion con' . ' ' . $service . ' ' . 'por favor intenta de nuevo' 
            ];
    
            return response()->json($data, $data['code']);
        }
    }
}
