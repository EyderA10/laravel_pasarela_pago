<?php

namespace App\Http\Controllers;

use App\Models\Comunicate;
use App\Models\Condicion;
use App\Models\Nosotros;
use App\Models\Pregunta;
use App\Models\Protagonista;
use App\Models\Testimonio;
use Illuminate\Http\Request;

class CustomController extends Controller
{
    public function getPreguntas()
    {
        $preguntas = Pregunta::all();

        if (!empty($preguntas) || is_object($preguntas)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'data' => $preguntas
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'No existe ninguna data'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getCondiciones()
    {

        $condiciones = Condicion::all();

        if (!empty($condiciones) || is_object($condiciones)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'data' => $condiciones
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'No existe ninguna data'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function saveComunicate(Request $request)
    {

        $asunto = $request->input('asunto');
        $descripcion = $request->input('descripcion');
        $user_id = $request->input('user_id');

        $comunicate = new Comunicate();
        $comunicate->asunto = $asunto;
        $comunicate->descripcion = $descripcion;
        $comunicate->user_id = $user_id;

        $comunicate->save();

        $data = [
            'status' => 'success',
            'code' => 201,
            'data' => $comunicate
        ];

        return response()->json($data, $data['code']);
    }

    public function getNosotros()
    {

        $nosotros = Nosotros::all();

        if (!empty($nosotros) || is_object($nosotros)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'data' => $nosotros
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'No existe ninguna data'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getProtagonistas()
    {

        $protagonistas = Protagonista::all();

        if (!empty($protagonistas) || is_object($protagonistas)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'data' => $protagonistas
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'No existe ninguna data'
            ];
        }

        return response()->json($data, $data['code']);
    }
    public function getTestimonios()
    {

        $testimonios = Testimonio::all();

        if (!empty($testimonios) || is_object($testimonios)) {
            $data = [
                'status' => 'succes',
                'code' => 200,
                'data' => $testimonios
            ];
        } else {
            $data = [
                'status' => 'failed',
                'code' => 500,
                'message' => 'No existe ninguna data'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
