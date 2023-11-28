<?php
namespace App\Controller;
use App\Model\Producto;
use App\Model\Registro;
use App\Model\AutentificadorJWT;

class ProductoController 
{
    public  function CrearUno($request, $response, $args)
    {
        $producto;
        $accion;
        $mensaje="Producto/s creado con exito";
        // Creamos el Producto
        if(isset($_FILES["data"])){
            $producto=Producto::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
            $mensaje=$mensaje . " con Archivo CSV";
            $accion="producto/s creado con archivo csv";
        }
        else{
            
            $parametros = $request->getParsedBody();
            $descripcion;
            $tipo;
            $timePrepacion;
            $precio;
            $accion;
           
            if(isset($parametros['descripcion'])){
                $descripcion = $parametros['descripcion'];
            }

            if(isset($parametros['categoria'])){
                $tipo=$parametros['categoria'];
            }
            if(isset($parametros['timePreparacion'])){
                $timePrepacion=$parametros['timePreparacion'];
            }
            if(isset($parametros['precio'])){
                $precio=$parametros['precio'];
            }
            $producto = new Producto($descripcion,$tipo,$timePrepacion,$precio);
            $accion=$producto->CreateInDB();
        }
        

        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
        $data=AutentificadorJWT::ObtenerData($header);
        $registro=new Registro($data->id,$accion);
        $registro->GuardarEnDB();
        }

        $payload = json_encode(array("mensaje" => $mensaje, "resultado"=>$producto));
        //die;
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Producto::TraerTodos();
        $payload = json_encode(array("listaProductos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        echo "\n test\n";
        var_dump($lista[1]);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['id'];
        $usuario = Producto::TraerUno($usr);

        $payload = json_encode($usuario);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function Descargar($request, $response, $args)
    {
        $r=Producto::DescargarDatosEnCSV("productos.csv");
        $payload = json_encode(array("Descargando Archivo CSV" => $r));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
