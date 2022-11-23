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
    public $codigoAlfa;
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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (detalle, estado, total, mesaId, codAlf) VALUES (:detalle, :estado, :total, :mesaId, :codAlf)");
        $consulta->bindValue(':detalle', $this->detalleJson, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':total', $this->total, PDO::PARAM_STR);
        $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
        $consulta->bindValue(':codAlf', $this->codigoAlfa, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT detalle, estado, total, mesaId, codAlf FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
    function generaCodigo5 () {
        $caracteres = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $codigo = '';
    
        for ($i = 1; $i <= 5; $i++) {
            $codigo = $codigo . $caracteres[$this->numeroAleatorio(0, 35)];
        }
    
        $this->codigoAlfa=$codigo;
    }
    
    function numeroAleatorio ($ninicial, $nfinal) {
        $numero = rand($ninicial, $nfinal);
        return $numero;
    }

}