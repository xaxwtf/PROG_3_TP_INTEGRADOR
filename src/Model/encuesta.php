<?php
class Encuesta{
    public $id;
    public $fecha;
    public $puntuacionMozo;
    public $puntuacionMesa;
    public $puntuacionRestaurant;
    public $puntuacionCocinero;
    public function __construct($puntuacionMesa,$puntuacionCocinero,$puntuacionMozo,$puntuacionRestaurant){
        $this->setPMesa($puntuacionMesa);
        $this->setPCocinero($puntuacionCocinero);
        $this->setPMozo($puntuacionMozo);
        $this->setPRestaurant($puntuacionRestaurant);
    }
    public function setPMozo($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionMozo=$puntuacion;
            $r=true;
        }
        return $r;
    }
    public function setPMesa($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionMesa=$puntuacion;
            $r=true;
        }
        return $r;
    }
    public function setPCocinero($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionCocinero=$puntuacion;
            $r=true;
        }
        return $r;
    }
    public function setPRestaurant($puntuacion){
        $r=false;
        if($puntuacion>0&&$puntuacion<11){
            $this->puntuacionRestaurant=$puntuacion;
            $r=true;
        }
        return $r;
    }
}