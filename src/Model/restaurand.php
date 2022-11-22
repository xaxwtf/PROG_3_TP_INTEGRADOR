<?php
require_once '/pedido.php';
require_once '/productos.php';
require_once '/empleados.php';
require_once '/mesa.php';
require_once '/encuesta.php';
class  Restaurand{
    public $name;
    public $historialPedidos;
    public $empleados;
    public $socios;
    public $listaProductos;
    public $mesas;
    public $historialEncuestas;

    public function __construct($name){
        $this->name=$name;
    }
    public function AltaEmpleado($fullname,$tipo){
        $nuevo=null;
        switch($tipo){
            case 1:
                $nuevo=new Mozo($myIdEmployee,$fullname);
                break;
            case 2:
                $nuevo=new Cocinero($myIdEmployee,$fullname);
                break;
            case 3:
                $nuevo=new Bartender($myIdEmployee,$fullname);
                break;
            case 4:
                $nuevo=new Cervecero($myIdEmployee,$fullname);
                break;
        }
        if($nuevo!=null){
            $this->empleados[count($this->empleados)]= $nuevo;
        } 
    }   
    public function BajaEmpleado($id){
        $r=false;
        for($i=0;i<count($this->empleados);$i++){
            if($this->empleados[$i]->id==$id){
                unset($this->empleados[$i]);
                $r=true;
                break;
            }
        }
        return $r;
    }
    public function EditEmpleado($id,$newFullName){
        $r=false;
        for($i=0;$i<count($this->empleados);$i++){
            if($this->empleados[$i]->id==$id){
                $this->empleados[$i]->fullName=$newFullName;
                $r=true;
                break;
            }
        }
        return $r;
    }
    public function ListarEmpleados(){
        return json_encode($this->empleados);
    }
}
?>