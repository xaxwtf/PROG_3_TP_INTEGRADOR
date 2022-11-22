<?php
namespace App\Controller;
use App\Model\Producto;

class ProductoController 
{
    public  function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $descripcion = $parametros['descripcion'];
        $tipo=$parametros['tipo'];
        $timePrepacion=$parametros['timePreparacion'];
        $precio=$parametros['precio'];

        // Creamos el Producto
        $usr = new Producto($descripcion,$tipo,$timePrepacion,$precio);
        $usr->CreateInDB();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        echo $payload;
        die;
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Producto::TraerTodos();
        $payload = json_encode(array("listaProductos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
