<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSocial;

class SocialiteController extends Controller
{

    public function returnUserData(Request $request)
    {

        $email = $request->input('email');
        $name = $request->input('name');
        $service = $request->input('service');
        $service_id = $request->input('serviceId');
        
        //se encarga de decirme si un usuario existe en la base de datos
        $user = $this->getExistingUser($service_id, $email, $service);

        //si no existe me crea el usuario
        if (!$user) {
            //y me retorna este user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => ''
            ]);
        }

        //registro en la tabla user social si el usuario se registro o se logueo con cuantas de redes sociales
        if ($this->needsToCreateSocial($user, $service)) {
            UserSocial::create([
                'user_id' => $user->id,
                'social_id' => $service_id,
                'service' => $service
            ]);
        }

        //al final retorno usuario ya sea el creado o el que ya existe
        $data = [
            'status' => 'success',
            'code' => 201,
            'data' => $user
        ];

        return response()->json($data, $data['code']);
    }

    //se encarga de hacer una query si el usuario posee una cuenta social o no
    //retorna bool
    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    //me retorna el usuario con el servicio por el cual se logueo o registro de manera dinamica
    public function getExistingUser($service_id, $email, $service)
    {
        //orWhereHas: busca dentro de una relacion de eloquent
        return User::where('email', $email)->orWhereHas('social', function ($q) use ($service_id, $service) {
            $q->where('social_id', $service_id)->where('service', $service);
        })->first();
    }
}
