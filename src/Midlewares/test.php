<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
$myFristMW=function (Request $_request, RequestHandler $handler):ResponseMW{
    $accion1= "";
    $response=$handler->handle($_request);
    $contenidoAPI =(string) $response->getBody();//obtengo el el contenido pasado
    $response=new ResponseMW();
    $accion2="";
    $response->getBody()->write("{$accion1} {$contenidoAPI} {$accion2}");///diseña la respuesta
    return $response;
}


?>