<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Registro{
    public $fecha;
    public $usuario;
    public $accion;

    public function __construct($usuario,$accion){
        $this->fecha= date("Y-m-d H:i:s");
        $this->usuario=$usuario;
        $this->accion=$accion;
    }
    public function GuardarEnDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_movimientos (fecha, usuario, accion) VALUES (:fecha, :usuario, :accion)");
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':accion', $this->accion, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha, usuario, accion  FROM registro_movimientos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
}