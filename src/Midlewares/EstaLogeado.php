<?php
namespace App\Midlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response ;

class EstaLogeado{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        $autorizacion=null;
        try{
            
           
            $response= new Response();
            $aux=json_encode(array("mensaje"=>"NO HAY USUARIOS LOGEADOS"));
            $response->getBody()->write($aux);
            $autorizacion = $request->getHeaderLine('Authorization');//recupero el token de autorizacion
            if(!empty($autorizacion)){
                
                $response=$handler->handle($request);//INVOCA AL SIGUIENTE MIDDLEWARE;    
            }    
        }
        catch(\Throwable $th){
            $response= new Response();
            $aux=json_encode(array("mensaje"=>"Error, ". $th->getMessage()));
            $response->getBody()->write($aux);
        }
        finally{
            return $response;
        }
        
        
        
    }
}



?>