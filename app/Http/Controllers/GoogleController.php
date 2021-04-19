<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function authLoginWithGoogle(Request $request)
    {
        $id_token = $request->get('idtoken');
        $CLIENT_ID = env('GOOGLE_CLIENT_ID');

        $client = new Google_Client(['client_id' => $CLIENT_ID]);
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $data = [
                'id' => $payload['sub'],
                'email' => $payload['email'],
                'name' => $payload['name'],
                'picture' => $payload['picture'],
                'given_name' => $payload['given_name']
            ];
            return $data;
        } else {
            return response()->json(['error' => 'Error Invalid Token ID'], 400);
        }
    }
}
