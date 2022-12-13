<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Producto{
    public $id;
    public $descripcion;
    public $categoria;
    public $timePreparacion;
    public $estado;
    public $precio;
    public function __construct($descripcion,$tipo,$timePreparacion,$precio){
        $this->id=0;
        $this->descripcion=$descripcion;
        $this->categoria=$tipo;
        $this->timePreparacion=$timePreparacion;
        $this->precio=$precio;
        $this->estado="en Preparacion";
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, tiempo_prom_preparacion, categoria, precio) VALUES (:descripcion,  :timePreparacion, :tipo, :precio)");
        
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo',$this->categoria, PDO::PARAM_STR);
        $consulta->bindValue(':timePreparacion',$this->timePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':precio',$this->precio, PDO::PARAM_STR);
        $consulta->execute();
        return "Creando Producto ID: ". $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tiempo_prom_preparacion, categoria, precio  FROM productos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }

    public static function CrearConArchivoCSV($file){
        $lista=ArchivosCSV::LeerArchivoCSV($file);
        foreach($lista as $r){
            $aux=new Producto($r["0"],$r["1"],$r["2"],$r["3"]);
            $aux->CreateInDB();
        }
        return "ha cargado datos con un archivo";
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