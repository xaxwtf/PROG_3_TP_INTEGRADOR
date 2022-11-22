<?php
namespace App\Controller;
use App\Model\Mesa;

class MesaController 
{
    public  function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];
        

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->CreateInDB();

        $payload = json_encode(array("mensaje" => "MESA creada con exito"));
        echo $payload;
        die;
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Mesa::TraerTodos();
        $payload = json_encode(array("listaMesas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
