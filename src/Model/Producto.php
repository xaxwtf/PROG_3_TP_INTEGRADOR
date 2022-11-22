<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Producto{
    public $id;
    public $descripcion;
    public $tipo;
    public $timePreparacion;
    public $estado;
    public $precio;
    public function __construct($descripcion,$tipo,$timePreparacion,$precio){
        $this->id=0;
        $this->descripcion=$descripcion;
        $this->tipo=$tipo;
        $this->timePreparacion=$timePreparacion;
        $this->precio=$precio;
        $this->estado="en Preparacion";
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion,tipo,tiempoPromdePreparacion,precio) VALUES (:descripcion, :tipo, :timePreparacion, :precio)");
        
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo',$this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':timePreparacion',$this->timePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':precio',$this->precio, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, tiempoPromdePreparacion, precio  FROM productos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }

}