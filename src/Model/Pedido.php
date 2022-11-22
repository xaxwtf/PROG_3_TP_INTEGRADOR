<?php

namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Pedido{

    protected $id;
    public $detalle;
    public $estado;
    public $total;
    private $detalleJson;
    public $mesaId;
    public function __construct(){
        $this->estado="en Preparacion";
        $this->detalle=array();
        
    }
    function addProducto($product){
        $this->detalle[count($this->detalle)]= $product;
    }
    function deleteProducto($detalle){
        for($i=0;i<count($detalle);$i++){
            if($detalle[$i]->detalle==$detalle){
                unset($detalle[$i]);
                break;
            }
        }
    }
    public function CargarDetalleConJson($string){
        $this->detalle=json_decode($string);
        $this->detalleJson=$string;
    }
    public function TiempoEstimadodePreparacion(){
        $timeFull=0;
        for($i=0;$i<count($this->detalle);$i++){
            if($this->detalle[$i]->estado=="en Preparacion" )
            $timeFull= $timeFull + $this->detalle[$i]->timePreparacion;
        }
        return $timeFull;
    }
    public function CalcularTotal(){
        $total=0;
        for($i=0;$i<count($this->detalle);$i++){
            $total= $total + $this->detalle[$i]->precio;
        }
        $this->total=$total;
    }
    public function ValidarEstado(){
        $r=false;
        for($i=0;$i<count($this->detalle);$i++){
            if($this->detalle[$i]->estado=="en Preparacion"){
                $r=true;
                break;
            }
        }
        if(!$r){
            $this->estado="en Preparacion";
        }
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (detalle, estado, total, mesaId ) VALUES (:detalle, :estado, :total, :mesaId)");
        $consulta->bindValue(':detalle', $this->detalleJson, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':total', $this->total, PDO::PARAM_STR);
        $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT detalle, estado, total, mesaId FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }

}