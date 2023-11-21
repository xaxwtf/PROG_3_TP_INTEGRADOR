<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Producto{
    public $id;
    public $descripcion;
    public $categoria;
    public $timePreparacion;
    public $precio;
    
    public function __construct($descripcion=null,$tipo=null,$timePreparacion=null,$precio=null){
        
        if($descripcion!=null){
            $this->descripcion=$descripcion;
        }
        if($tipo!=null){
            $this->categoria=$tipo;
        }
        if($timePreparacion!=null){
            $this->timePreparacion=$timePreparacion;
        }
        if($precio!=null){
            $this->precio=$precio;
        }
        
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, tiempo_prom_preparacion, categoria, precio) VALUES (:descripcion,  :timePreparacion, :tipo, :precio)");
        $this->estado="en Preparacion";
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo',$this->categoria, PDO::PARAM_STR);
        $consulta->bindValue(':timePreparacion',$this->timePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':precio',$this->precio, PDO::PARAM_STR);
        $consulta->execute();
        $this->id=$objAccesoDatos->obtenerUltimoId();
        return "Creando Producto ID: ". $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tiempo_prom_preparacion, categoria, precio  FROM productos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,"App\Model\Producto");
    }

    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tiempo_prom_preparacion, categoria, precio  FROM productos where id=:id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Producto');
        return $consulta->fetch();
        
    }

    public static function CrearConArchivoCSV($file){
        $lista=ArchivosCSV::LeerArchivoCSV($file);
        $ret=[];
        foreach($lista as $r){
            $aux=new Producto($r["0"],$r["1"],$r["2"],$r["3"]);
            $ret[count($ret)]=$aux;
            $aux->CreateInDB();
        }
        return $ret;
    }
    public static function DescargarDatosEnCSV($namefile){
        $lista=Producto::TraerTodos();
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id .",". $lista[$i]->descripcion .",". $lista[$i]->tiempo_prom_preparacion .",". $lista[$i]->categoria  .",". $lista[$i]->precio ."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }
}