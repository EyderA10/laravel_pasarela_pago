<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use App\Models\User;

class JwtAuth {

    private $key;

    public function __construct()
    {
        $this->key = env('JWT_AUTH_SECRET_TOKEN');
    }

    public function generateToken($email, $password, $getToken = false){

        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        $sign_in = false;
        if(is_object($user) && !empty($user)){
            $sign_in = true;
        }

        if($sign_in){

            $payload = [
                'sub' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_name' => $user->last_name,
                'num_telf' => $user->num_telf,
                'avatar' => $user->avatar,
                'iat' => time(),
                'exp' => time() . 7200
            ];

            $jwt = JWT::encode($payload, $this->key, 'HS256');
            $decode = JWT::decode($jwt, $this->key, ['HS256']);


            if($getToken){
                $data = $decode;
            }else{
                $data = $jwt;
            }


        }else{
            return false;
        }

        return $data;
    }


    public function checkToken($jwt, $getIdentity = false){

        $auth = false;

        try{

            $jwt = str_replace('"', '', $jwt);

            $decode = JWT::decode($jwt, $this->key, ['HS256']);

        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }

        if(!empty($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decode;
        }

        return $auth;

    }
}
