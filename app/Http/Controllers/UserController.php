<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\JwtAuth;
use App\Models\User;

class UserController extends Controller
{

    public function signUp(Request $request)
    {
        $json = $request->input('json', null);
        $params_ob = json_decode($json);
        $params = json_decode($json, true);

        if (empty($params) && empty($params_ob)) {

            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'Sorry!, The data must be required'
            ];
        } else {

            $params = array_map('trim', $params);

            $validate = Validator::make($params, [
                'name' => 'required|alpha',
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users',
                'password' => 'required|max:10',
                'confirm' => 'required|max:10|same:password',
            ]);

            if ($validate->fails()) {
                $data = [
                    'status' => 'failed',
                    'code' => 400,
                    'errors' => $validate->errors()
                ];
            } else {

                $pwd = hash("sha256", $params_ob->password);

                $user = new User();
                $user->name = $params_ob->name;
                $user->email = $params_ob->email;
                $user->password = $pwd;

                $user->save();

                $jwt = new JwtAuth();

                $token = $jwt->generateToken($user->email, $user->password);

                $data = [
                    'status' => 'success',
                    'code' => 201,
                    'user' => $user,
                    'token' => $token
                ];
            }
        }
        return response()->json($data, $data['code']);
    }

    public function signIn(Request $request)
    {
        $json = $request->input('json', null);
        $params_ob = json_decode($json);
        $params = json_decode($json, true);

        if (empty($params) && empty($params_ob)) {

            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'Sorry!, The data must be required'
            ];
        } else {

            $params = array_map('trim', $params);

            $validate = Validator::make($params, [
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                'password' => 'required|max:10',
            ]);

            if ($validate->fails()) {
                $data = [
                    'status' => 'failed',
                    'code' => 400,
                    'errors' => $validate->errors()
                ];
            } else {

                $pwd = hash("sha256", $params_ob->password);

                $jwt = new JwtAuth();

                $token = $jwt->generateToken($params_ob->email, $pwd);

                if (!empty($params_ob->getToken)) {
                    $token = $jwt->generateToken($params_ob->email, $pwd, true);
                }

                if(!$token){
                    $data = [
                        'status' => 'failed',
                        'code' => 404,
                        'message' => 'This Credentials are not exists!'
                    ];

                }else{
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'token' => $token
                    ];
                }
            }
        }
        return response()->json($data, $data['code']);
    }
}
