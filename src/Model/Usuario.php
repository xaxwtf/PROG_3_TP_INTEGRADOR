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

    public function __construct($id=null, $fullName=null, $rol=null, $password=null, $user=null){
        if($id!=null){
            $this->id=$id;
        }
        if($fullName!=null){
            $this->fullName=$fullName;
        }
        if($rol!=null){
            $this->rol= $rol;
        }
        if($password!=null){
            $this->password= password_hash($password, PASSWORD_DEFAULT);
        }
        if($user!=null){
            $this->user=$user;
        }
    }
    public static function CrearConArchivoCSV($file){
        $lista=ArchivosCSV::LeerArchivoCSV($file);
        $ret=[];
        foreach($lista as $r){
            $aux=new Usuario(null,$r["0"],$r["1"],$r["2"],$r["3"]);
            $ret[count($ret)]=$aux;
            $aux->CreateInDB();
        }
        return $ret;
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
        $this->id=$objAccesoDatos->obtenerUltimoId();
        return "Creando Usuario ID: ". $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol, password, user FROM users");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,"App\Model\Usuario");
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol, password, user FROM users  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Usuario');
        return $consulta->fetch();
        
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

