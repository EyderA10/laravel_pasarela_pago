<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ordene;
use App\Models\OrdenesProducto;

class OrdenController extends Controller
{
    public function saveOrdenByUser(Request $request)
    {
        $tiendas = $request->get('tiendas');

        foreach ($tiendas as $tienda) {
            foreach ($tienda['productos'] as $producto) {
                $orden = Ordene::create([
                    'tienda_id' => $tienda['tienda_id'],
                    'state' => 'pending',
                    'lat' => $producto['lat'],
                    'lon' => $producto['lon'],
                    'type' => $producto['type'],
                    'nombre_recepcion' => $producto['nombre'],
                    'num_cel_recepcion' => $producto['telefonoCelular'],
                    'hora' => $producto['hora']
                ]);

                $orden_products = OrdenesProducto::create([
                    'producto' => $producto['producto'],
                    'descripcion' => $producto['descripcion'],
                    'precio_a' => $producto['precio_a'],
                    'precio_b' => $producto['precio_b'],
                    'imagen' => $producto['imagen'],
                    'cantidad' => $producto['cantidad'],
                    'orden_id' => $orden->id
                ]);
            }
        }

        return response()->json(compact('orden', 'orden_products'), 200);
    }
}
