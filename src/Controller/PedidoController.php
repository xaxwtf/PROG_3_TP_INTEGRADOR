<?php
namespace App\Controller;
use App\Model\Pedido;
use App\Model\Mesa;
use App\Model\Registro;
use App\Model\AutentificadorJWT;
use App\Model\Detalle;

class PedidoController 
{
    public  function CrearUno($request, $response, $args)
    {
        
        $accion;
        $mostra=null;
        if(isset($_FILES["data"])){
            $accion=Pedido::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
            $mostra=$accion;
            
        }
        else if( isset($_POST["detalle"]) && isset($_POST["mesaId"]) && isset($_POST["cliente"])){

            $parametros = $request->getParsedBody();
            $detalle = $parametros['detalle'];
            $mesa=$parametros['mesaId'];
            $cliente=$parametros['cliente'];
            $pedido = new Pedido();
            $pedido->CargarDetalleConJson($detalle);//recibimos en modo json
            $pedido->mesaId=$mesa;
            $pedido->cliente=$cliente;
            $pedido->estado="en Preparacion";
            $pedido->fecha_emision=date("Y-m-d H:i:s");
            $pedido->generaCodigo5();
            Mesa::CambiarEstadoMesa($pedido->mesaId,"Cliente Esperando Pedido");
            $accion=$pedido->CreateInDB();
            $mostra=$pedido;
        }
        else{

            $accion= "Se Ha Intentado Dar de Alta Un Producto, pero hubo un Error!";
            $mostra="\n Error, Adjunte un archivo u mande todos los Parametros\n";
            
        }
        if(isset($_FILES["imagen"])){
            $newNameFile=date("Y-m-d H:i:s") .".". pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
            $destino = "./Imagenes/" . $newNameFile;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino);
        }
        else{
            
            $destino = "N/A";
            
        }


        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,$accion);
        $registro->GuardarEnDB();
        }

        $payload = json_encode(array("PedidoCreado" => $mostra));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Pedido::TraerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $usr = $args['id'];
        $usuario = Pedido::TraerUno($usr);
        $payload=json_encode(array("Pedido"=>$usuario));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function TestUltimoId($request, $response, $args){
        $lista=Pedido::TraerUltimoId();
        $payload = json_encode(array("listaPedidos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function EntregarPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $recuperado=Pedido::TraerUnoXCodAlfa($p);
        //var_dump($recuperado);
        $total;
        $respuesta;
        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro;
        }
        $lista=Detalle::ConsultaTodoDetalleDeUnPedido($p);
        if(count($lista)==0){
            $total=Pedido::ObtenerTotal($recuperado->id);
            
            $test=Pedido::EntregarPedido($p);
            
            $registro=new Registro($data->id,"el usuario ha entregado el pedido ID:". $recuperado->id);
            $respuesta=array("Mensaje"=>"Pedido Entregado","resultado"=> $test, "Total"=>$total);
        }
        else{

            $respuesta= array("Mensaje"=>"Error, el Pedido no se puede entregar por que hay productos pendientes", "Detalle"=>$lista);
            $registro=new Registro($data->id,"el usuario ha entregado el pedido ID:". $recuperado->id);
        }

        $registro->GuardarEnDB();
        //Detalle::DeclararListoTodoElDetalle($recuperado->id);

        $payload = json_encode($respuesta);///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function CobrarPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $recuperado=Pedido::CobrarPedido($p);
        $total=$recuperado->btenerTotal();
        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,"el usuario ha cobrado el pedido ID:". $recuperado->id);
        $registro->GuardarEnDB();
        }
        
        $payload = json_encode(array("Pedido Finalizado" => $recuperado, "Total Facturado"=>$total->total));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function InformarDemora($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $d= $parametros['time'];
        $recuperado=Pedido::DemorarPedido($p,$d);
        $total=$recuperado->ObtenerTotal();
        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,"el Usuario ha notificado una demora de ". $d . "minutos en el pedido ID:". $recuperado->id);
        $registro->GuardarEnDB();
        }

        $payload = json_encode(array("Pedido DEMORADO" => $recuperado, "Total"=>$total->total));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
