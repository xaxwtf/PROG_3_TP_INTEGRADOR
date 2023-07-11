<?php
namespace App\Controller;
use App\Model\Producto;
use App\Model\Registro;
use App\Model\AutentificadorJWT;

class ProductoController 
{
    public  function CrearUno($request, $response, $args)
    {
        
        $accion;
        $mensaje="Usuario creado con exito";
        // Creamos el Producto
        if(isset($_FILES["data"])){
            $accion=Producto::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
        }
        else{
                      
            $parametros = $request->getParsedBody();
            $descripcion;
            $tipo;
            $timePrepacion;
            $precio;
            $producto;
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

        $payload = json_encode(array("mensaje" => $mensaje));
        echo $payload;
        die;
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Producto::TraerTodos();
        $payload = json_encode(array("listaProductos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}
