<?php

namespace App\Model;
use App\Model;
use App\Db\AccesoDatos;
use PDO;
class Usuario{

    public $id;
    public $fullName;
    public $rol;
    public $password;
    public $user;

    public function __construct($id, $fullName, $rol, $password, $user){
        $this->id=$id;
        $this->fullName=$fullName;
        $this->rol= $rol;
        $this->user=$user;
        $this->password= password_hash($password, PASSWORD_DEFAULT);
    }
    public static function CrearConArchivoCSV($file){
        $lista=ArchivosCSV::LeerArchivoCSV($file);
        foreach($lista as $r){
            $aux=new Usuario(null,$r["0"],$r["1"],$r["2"],$r["3"]);
            $aux->CreateInDB();
        }
        return "ha cargado datos con un archivo";
    }
    public static function DescargarDatosEnCSV($namefile){
        $lista=Usuario::TraerTodos();
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id .",". $lista[$i]->fullName .",". $lista[$i]->rol .",". $lista[$i]->password  .",". $lista[$i]->user ."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO users (fullName, rol,  password, user) VALUES (:fullName, :rol,  :pass , :user)");
        $consulta->bindValue(':fullName', $this->fullName, PDO::PARAM_STR);
        $consulta->bindValue(':rol',$this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':pass',$this->password, PDO::PARAM_STR);
        $consulta->bindValue(':user',$this->user, PDO::PARAM_STR);
        $consulta->execute();
        return "Creando Usuario ID: ". $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol, password, user FROM users");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol, password, user FROM users  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
   
    public static function TraerUnoxUserAndPass($user,$pass){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol,  password, user FROM users  where user= :user");
        $consulta->bindValue(':user',$user, PDO::PARAM_STR);
        $consulta->execute();
        $rec=$consulta->fetch(PDO::FETCH_ASSOC);
        $r=null;
        if( password_verify( $pass, $rec['password'])){
            $r= $rec;
        }
        return $r;
    }
    public static function CambiarEstadoUsuario($id , $estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.users SET estado = :estado WHERE (id = :id)");
        $consulta->bindValue(':estado',$estado, PDO::PARAM_STR);
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        return $consulta->execute();
    }
    
}

