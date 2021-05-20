<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function registro(Request $request)
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
                'last_name' => 'required|alpha',
                'num_telf' => 'required|string',
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users',
                'password' => 'required|string|max:100',
                'confirm' => 'required|string|max:100|same:password',
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
                $user->last_name = $params_ob->last_name;
                $user->num_telf = $params_ob->num_telf;
                $user->email = $params_ob->email;
                $user->password = $pwd;

                $user->save();

                $data = [
                    'status' => 'success',
                    'code' => 201,
                    'user' => $user,
                ];
            }
        }
        return response()->json($data, $data['code']);
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('avatar');

        $validate = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,png,jpeg,gif'
        ]);

        if (!$file || $validate->fails()) {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'errors' => $validate->errors()
            ];
        } else {
            $filename = time() . $file->getClientOriginalName();
            Storage::disk('user')->put($filename, File::get($file));

            $data = [
                'status' => 'success',
                'code' => 201,
                'avatar' => $filename
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function profile($id)
    {
        $user = User::find($id);

        if (isset($user) || is_object($user) || !empty($user)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'user' => $user
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 404,
                'message' => 'Error, this is id is invalid or not exist user with this id'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getFile($image)
    {

        $isset_file = Storage::disk('user')->exists($image);

        if ($isset_file) {
            $file = Storage::disk('user')->get($image);
            return new Response($file, 200);
        } else {
            $data = [
                'status' => 'failed',
                'code' => 404,
                'message' => 'Error to get this image because not exists!'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {

        $json = $request->input('json', null);
        $params_ob = json_decode($json);
        $params = json_decode($json, true);

        if (empty($params_ob) || empty($params)) {
            $data = [
                'status' => 'failed',
                'code' => 400,
                'message' => 'the data is required'
            ];
        } else {

            $params = array_map('trim', $params);

            $validate = Validator::make($params, [
                'name' => 'required|alpha',
                'last_name' => 'required|alpha',
                'num_telf' => 'required|numeric',
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,' . $id,
                'avatar' => 'image|mimes:jpg,png,jpeg,gif'
            ]);

            if ($validate->fails()) {
                $data = [
                    'status' => 'failed',
                    'code' => 500,
                    'errors' => $validate->errors()
                ];
            } else {
                unset($params['id']);
                unset($params['confirm']);
                unset($params['created_at']);
                unset($params['remember_token']);

                User::where('id', $id)->update($params);

                $data = [
                    'status' => 'succes',
                    'code' => 200,
                    'changes' => $params
                ];
            }
            return response()->json($data, $data['code']);
        }
    }
}
