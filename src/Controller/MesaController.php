<?php
namespace App\Controller;
use App\Model\Usuario;
use App\Model\Mesa;
use App\Model\Pedido;
use App\Model\Informes30;
use App\Model\Detalle;

class MesaController 
{
    public  function CrearUno($request, $response, $args)
    {
        // Creamos la mesa
        $mesa = new Mesa();
        $accion=$mesa->CreateInDB();

        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,$accion);
        $registro->GuardarEnDB();
        }

        $payload = json_encode(array("mensaje" => "MESA creada con exito"));
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
    public function CerrarUnaMesa($request, $response, $args){
        $parametros = $request->getParsedBody();
        Mesa::CambiarEstadoMesa($parametros["mesa"],"Cerrada");

        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,"el usuario ha cerrado la mesa". $parametros["mesa"]);
        $registro->GuardarEnDB();
        }

        $payload = json_encode(array("mensaje" => "MESA cerrada"));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function Testeos($request, $response, $args){
        //Pedido::CrearConArchivoCSV($_FILES["test"]["tmp_name"]);
        
        //Pedido::DescargarDatosEnCSV("TESTEANDOEscribirPedido.CSV");
        //$aux=Informes30::MesaMasUsada();
        $lista=Detalle::ConsultaTodoDetalleDeUnPedido($_POST["test"]);
        $payload = json_encode(array("resultado" => $lista));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
