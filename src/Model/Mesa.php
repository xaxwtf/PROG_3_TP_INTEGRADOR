<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Mesa{
    public $id;
    public $estado;
    public function __construct(){
        $this->estado="libre";
    }
 

    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado) VALUES (:estado)");
        
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado  FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
}