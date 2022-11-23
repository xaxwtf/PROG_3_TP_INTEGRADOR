<?php
namespace App\Controller;
use App\Model\Pedido;

class PedidoController 
{
    public  function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $detalle = $parametros['detalle'];
        $mesa=$parametros['mesaId'];
        // Creamos el Producto
        $usr = new Pedido();
        $usr->CargarDetalleConJson($detalle);//recibimos en modo json
        $usr->mesaId=$mesa;
        $usr->CalcularTotal();
        $usr->ValidarEstado();
        $usr->generaCodigo5();
        //$usr->total=2000;
        //$usr->estado="en Preparacion";

        $usr->CreateInDB();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        echo $payload;
        die;
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Pedido::TraerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
