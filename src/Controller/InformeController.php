<?php
namespace App\Controller;
use App\Model\Usuario;
use App\Model\Mesa;
use App\Model\Pedido;
use App\Model\Informes30;

class InformeController 
{
    public  function GenerarLogo($request, $response, $args)
    {
        // Creamos la mesa
        Informes30::Logo();

        $payload = json_encode(array("mensaje" => "LOGO creado con exito"));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public  function MejoresEncuestas($request, $response, $args)
    {
        $lista=Informes30::MejoresEncuestas();
        $payload = json_encode(array("MejoresEncuestas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MesaMasUsada($request, $response, $args)
    {
        $resultado=Informes30::MesaMasUsada();
        $payload = json_encode(array("Mesa mas Usada" => $resultado));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function PedidosConDemora($request, $response, $args){
        $resultado=Informes30::PedidosEntregadosConDemora();
        $payload = json_encode(array("pedidosEntregadosConDemora" => $resultado));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function CantidadDeAccionesxUsuario($request, $response, $args){
        $resultado=Informes30::CantidadDeAccionesxUsuario();
        $payload = json_encode(array("Acciones x Usuario" => $resultado));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function UnidadesVendidasxProducto($request, $response, $args){
        $resultado=Informes30::UnidadesVendidasxProducto();
        $payload = json_encode(array("Unidades Vendidas x Producto" => $resultado));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ListadeLogeos($request, $response, $args){
        $resultado=Informes30::ListadeLogeos();
        $payload = json_encode(array("LISTA DE LOGEOS" => $resultado));
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>