<?php
namespace App\Model;
use App\Model;
use App\Db\AccesoDatos;
use PDO;

class Encuesta{
    public $id;
    public $fecha;
    public $puntuacionMozo;
    public $puntuacionMesa;
    public $puntuacionRestaurant;
    public $puntuacionCocinero;
    public $resenia;
    public $codigoPedido;
    public function __construct($puntuacionMozo,$puntuacionMesa,$puntuacionCocinero,$puntuacionRestaurant,$resenia,$codigoPedido){
        $this->fecha= date("Y-m-d H:i:s");
        $this->setPMesa($puntuacionMesa); 
        $this->setPCocinero($puntuacionCocinero);
        $this->setPMozo($puntuacionMozo);
        $this->setPRestaurant($puntuacionRestaurant);
        $this->resenia=$resenia;
        $this->codigoPedido=$codigoPedido;
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (fecha,puntuacionMozo, puntuacionMesa, puntuacionCocinero, puntuacionRestaurand,codigoPedido) VALUES (:fecha, :pMozo,:pMesa, :pCocinero, :pRest ,:cod)");
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':pMozo', $this->puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':pMesa', $this->puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':pCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':pRest',  $this->puntuacionRestaurant, PDO::PARAM_INT);
        $consulta->bindValue(':cod', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public function setPMesa($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionMesa=$puntuacion;
            $r=true;
        }
        else if($puntuacion>10){
            $this->puntuacionMesa=10;
        }
        else if($puntuacion<0){
            $this->puntuacionMesa=0;
        }
        return $r;
    }
    public function setPMozo($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionMozo=$puntuacion;
            $r=true;
        }
        else if($puntuacion>10){
            $this->puntuacionMozo=10;
        }
        else if($puntuacion<0){
            $this->puntuacionMozo=0;
        }
        return $r;
    }
    public function setPRestaurant($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionRestaurant=$puntuacion;
            $r=true;
        }
        else if($puntuacion>10){
            $this->puntuacionRestaurant=10;
        }
        else if($puntuacion<0){
            $this->puntuacionRestauran=0;
        }
        return $r;
    }
    public function setPCocinero($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionCocinero=$puntuacion;
            $r=true;
        }
        else if($puntuacion>10){
            $this->puntuacionCocinero=10;
        }
        else if($puntuacion<0){
            $this->puntuacionCocinero=0;
        }
        return $r;
    }

    
    
}