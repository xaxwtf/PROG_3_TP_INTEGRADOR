<?php
namespace App\Controller;

use App\Model\Usuario;
use App\Model\AutentificadorJWT;
use App\Model\Detalle;
use App\Model\Registro;

class UsuarioController 
{
    public function cargarUno($request, $response, $args)
    {
        $accion;
        $usr;
        
        if(isset($_FILES["data"])){
          $usr=Usuario::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
          $accion="usuario Creado con ArchivoCsv";
        }
        else{
          $parametros = $request->getParsedBody();
          $fullName = $parametros['fullName'];
          $rol=$parametros['rol'];
          $user=$parametros['user'];
          $pass=$parametros['pass'];
          $usr = new Usuario(1, $fullName, $rol, $user, $pass);
          $accion=$usr->CreateInDB();
        }
        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
          $data=AutentificadorJWT::ObtenerData($header);
          $registro=new Registro($data->id,$accion);
          $registro->GuardarEnDB();
        }

        $payload = json_encode(array("mensaje" => "Usuario creado con exito", "resultado"=>$usr));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::TraerUno($usr);

        $payload = json_encode($usuario);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista=Usuario::TraerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function LogearUsuario($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usr = $parametros['user'];
      $pass=$parametros['pass'];
      $r=Usuario::TraerUnoxUserAndPass($usr,$pass);
      if ($r!=null){
        $token=AutentificadorJWT::CrearToken($r);
        $registro=new Registro($r["id"] ,"el usuario se ha logeado!");
        $registro->GuardarEnDB();

        $payload = json_encode(array("jwt"=>$token));
      } else {

        $payload = json_encode(array("mensaje" => "ERROR! usuario no encontrado"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function Perfil($request, $response, $args){

      $header = $request->getHeaderLine('Authorization');
      $payload="no existe el usuario";
      if(!empty($header)){
        $info=AutentificadorJWT::ObtenerData($header);      
        $payload = json_encode(array("Usuario" =>$info));
      }
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarListaPendientes($request, $response, $args){
        $autorizacion = $request->getHeaderLine('Authorization');
        $lista;
        $data=AutentificadorJWT::ObtenerData($autorizacion);
        switch($data->rol){
          case "Cocinero":
            $lista["Cocina"]=Detalle::ObtenerListaPendientesXCategoria("Cocina");///debe varias la categoria segun el token
            $lista["CandyBar"]=Detalle::ObtenerListaPendientesXCategoria("CandyBar");
            break;
          case "Cervecero":
            $lista["Choperas"]=Detalle::ObtenerListaPendientesXCategoria("Choperas");
            break;
          case "Bartender":
            $lista["TragosVinos"]=Detalle::ObtenerListaPendientesXCategoria("TragosyVinos");
            break;
          case "Admin":
            $lista["Cocina"]=Detalle::ObtenerListaPendientesXCategoria("Cocina");
            $lista["CandyBar"]=Detalle::ObtenerListaPendientesXCategoria("CandyBar");
            $lista["Choperas"]=Detalle::ObtenerListaPendientesXCategoria("Choperas");
            $lista["TragosVinos"]=Detalle::ObtenerListaPendientesXCategoria("TragosyVinos");
            break;
          case "Socio":
            $lista["Cocina"]=Detalle::ObtenerListaPendientesXCategoria("Cocina");
            $lista["CandyBar"]=Detalle::ObtenerListaPendientesXCategoria("CandyBar");
            $lista["Choperas"]=Detalle::ObtenerListaPendientesXCategoria("Choperas");
            $lista["TragosVinos"]=Detalle::ObtenerListaPendientesXCategoria("TragosyVinos");
            break;
        }

        
        $payload = json_encode($lista);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TomarUnPendiente($request, $response, $args){
      $autorizacion = $request->getHeaderLine('Authorization');
      $uno;
      $data=AutentificadorJWT::ObtenerData($autorizacion);

      
      switch($data->rol){
        case "Cocinero":
          $r=rand(0, 1);
          if($r==1){
            $uno=Detalle::TomarPrimerPendienteDeCategoria("cocina");
          }
          else{
            $uno=Detalle::TomarPrimerPendienteDeCategoria("CandyBar");
          }
          break;
        case "Cervecero":
          $uno=Detalle::TomarPrimerPendienteDeCategoria("Choperas");
          break;
        case "Bartender":
          $uno=Detalle::TomarPrimerPendienteDeCategoria("TragosyVinos");
          break;
        case "Admin":
          $r=rand(1, 4);
          switch($r){
            case 1:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("cocina");
              break;
            case 2:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("CandyBar");
              break;
            case 3:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("Choperas");
              break;
            case 4:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("TragosyVinos");
              break;
          }
          break;
        case "Socio":
          $r=rand(0, 3);
          switch($r){
            case 0:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("cocina");
              break;
            case 1:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("CandyBar");
              break;
            case 2:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("Choperas");
              break;
            case 3:
              $uno=Detalle::TomarPrimerPendienteDeCategoria("TragosyVinos");
              break;
          }
          break;
      }
      
      $registro=new Registro($data->id,"el usuario ha tomado el producto pendiente id_detalle". $uno->producto_id);
      $registro->GuardarEnDB();
      Usuario::CambiarEstadoUsuario($data->id,"Ocupado");
      $payload = json_encode($uno);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }
  public function FinalizarUnPendiente($request, $response, $args){
    $parametros = $request->getParsedBody();
    $id = $parametros['id_detalle'];
    $aux=Detalle::NotificarFinalizacionDeProducto($id);

    $header = $request->getHeaderLine('Authorization');
    if(!empty($header)){
      $data=AutentificadorJWT::ObtenerData($header);
      $registro=new Registro($data->id,"el usuario ha Notificado la finalizacion del producto pendiente ID_Detalle:". $id);
      $registro->GuardarEnDB();
      Usuario::CambiarEstadoUsuario($data->id,"Libre");
    }
    

    $payload = json_encode(array("PRODUCTO TERMINADO" =>$aux));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}
    
}
