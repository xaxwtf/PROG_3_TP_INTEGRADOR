<?php

namespace App\Model;
use App\Db\AccesoDatos;
use PDO;
class Empleado{

    public $id;
    public $fullName;
    public $rol;
    public $pendientes;

    public function __construct($id, $fullName, $rol){
        $this->id=$id;
        $this->fullName=$fullName;
        $this->rol= $rol;
    }
    public function setFullName($fullName){
        $this->fullName=$fullName;
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (fullName,rol,id_pendientes) VALUES (:fullName, :rol, :id_pendientes)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':fullName', $this->fullName, PDO::PARAM_STR);
        $consulta->bindValue(':rol',$this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':id_pendientes',1, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fullName, rol, id_pendientes FROM empleados");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
}

