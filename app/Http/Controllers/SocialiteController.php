<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use App\Models\UserSocial;

class SocialiteController extends Controller
{
    //muestra la pagina de autenticacion con google

    public function redirectToProvider(Request $request)
    {
        $service = $request->service;

        if($service === 'github' || $service === 'twitter') {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'Error no puedes acceder a este servicio'
            ];

            return response()->json($data, $data['code']);
        }

        $redirect_url =  Socialite::driver($service)->stateless()->redirect()->getTargetUrl();

        return response()->json(['redirect_url' => $redirect_url]);
    }

    //me retorna los datos del usuario

    public function handleProviderCallback(Request $request)
    {

        $service = $request->service;

        try {

            $serviceUser = Socialite::driver($service)->stateless()->user();

        } catch (\Exception $e) {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'error' => 'Error al intentar iniciar sesion con' . ' ' . $service . ' ' . 'por favor intenta de nuevo' 
            ];
    
            return response()->json($data, $data['code']);
        }

        //se encarga de decirme si un usuario existe en la base de datos
        $email = $serviceUser->getEmail();

        $user = $this->getExistingUser($serviceUser, $email, $service);

        //si no existe me crea el usuario
        if (!$user) {
            //y me retorna este user
            $user = User::create([
                'name' => $serviceUser->getName(),
                'email' => $email,
                'password' => ''
            ]);
        }

        //registro en la tabla user social si el usuario se registro o se logueo con cuantas de redes sociales
        if ($this->needsToCreateSocial($user, $service)) {
            UserSocial::create([
                'user_id' => $user->id,
                'social_id' => $serviceUser->getId(),
                'service' => $service
            ]);
        }

        //al final retorno el token que me devuelve servicio y el usuario
        $data = [
            'status' => 'success',
            'code' => 201,
            'data' => $user,
            'token' => $serviceUser->token, 
            'refreshToken' => $serviceUser->refreshToken, 
            'expiresIn' => $serviceUser->expiresIn,
            'avatar' => $serviceUser->getAvatar() 
        ];

        return response()->json($data, $data['code']);
    }

    //se encargar de hacer una query si el usuario posee una cuenta social o no
    //retorna bool
    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    //me retorna el usuario con el servicio por el cual se logueo o registro de manera dinamica
    public function getExistingUser($serviceUser, $email, $service)
    {
        //orWhereHas: busca dentro de una relacion de eloquent
        return User::where('email', $email)->orWhereHas('social', function ($q) use ($serviceUser, $service) {
            $q->where('social_id', $serviceUser->getId())->where('service', $service);
        })->first();
    }
}
