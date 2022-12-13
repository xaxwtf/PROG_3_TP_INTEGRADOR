<?php
namespace App\Controller;
use App\Model;
use App\Model\Encuesta;

class ClienteController 
{
    public  function ConsultarTiempoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codAlf'];

        $payload = json_encode(array("mensaje" => "TIEMPO DE PEDIDO"));
        echo $payload;
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function SolicitarTotal($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $parametros['codAlf'];
        Mesa::CambiarEstadoMesa($codigo,"con cliente pagando");
        $r=Cliente::TraerUnoXCodAlfa($codigo);
        $payload = json_encode(array("Total" => $r->CalcularTotal()));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public  function Encuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $parametros['codAlf'];
        $pMesa= $parametros["pMesa"];
        $pMozo= $parametros["pMozo"];
        $pRest=$parametros["pRestaurand"];
        $comentario=$parametros["comentario"];
        $pCocinero=$parametros["pCocinero"];

        $encu= new Encuesta($pMozo,$pMesa,$pCocinero, $pRest,$comentario,$codigo);
        $encu->CreateInDB();
        $payload = json_encode(array("ENCUESTA CREADA" => $encu));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>