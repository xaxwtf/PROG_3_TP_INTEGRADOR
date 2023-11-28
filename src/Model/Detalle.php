<?php

namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Detalle{

    public function CargarConJson($string){
        $this->lista=json_decode($string);
    }
    public  static function AgregarProducto($codPedido, $idProducto){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO detalle (id_pedido, id_producto, estado ) VALUES (:id_pedido, :id_producto, :estado)");
        $consulta->bindValue(':id_pedido', $codPedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':estado',"pendiente",PDO::PARAM_STR);
        $consulta->execute();
        
    }
    public static function RecuperarDetalle($idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, productos.tiempo_prom_preparacion, productos.categoria , productos.precio, detalle.estado FROM tp_integrador.detalle 
        inner join tp_integrador.productos on tp_integrador.productos.id=tp_integrador.detalle.id_producto
        where detalle.id_pedido=:idP");
        $consulta->bindValue(':idP',$idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $recuperado=$consulta->fetchAll(PDO::FETCH_CLASS);
        return $recuperado;
    }

    public static function RecuperarDetallePendiente($idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, productos.tiempo_prom_preparacion, productos.categoria , productos.precio, detalle.estado FROM tp_integrador.detalle 
        inner join tp_integrador.productos on tp_integrador.productos.id=tp_integrador.detalle.id_producto
        where detalle.id_pedido=:idP and detalle.estado= 'pendiente'");
        $consulta->bindValue(':idP',$idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $recuperado=$consulta->fetchAll(PDO::FETCH_CLASS,"App\Model\Detalle");
        return $recuperado;
    }
    public static function RecuperarDetalleListo($idProducto){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, tiempo_prom_preparacion, categoria, precio, detalle.estado FROM tp_integrador.detalle 
        inner join tp_integrador.productos on tp_integrador.productos.id= detalle.id_producto
        where tp_integrador detalle.id_pedido=:idP and detalle.estado= 'listo' ");
        $consulta->bindValue(':idP',$idProducto, PDO::PARAM_INT);
        $consulta->execute();
        $recuperado=$consulta->fetchAll(PDO::FETCH_CLASS,"App\Model\Detalle");
        return $recuperado;
    }

    public static function ObtenerListaPendientesXCategoria($categoriaProducto){
        $objAccesoDatos= AccesoDatos::obtenerInstancia();
        $consulta=$objAccesoDatos->prepararConsulta("SELECT (detalle.id) as id_detalle , detalle.id_pedido, (productos.id)as producto_id, productos.descripcion,  productos.tiempo_prom_preparacion, productos.categoria, productos.precio, detalle.estado from detalle
        inner join productos on productos.id = detalle.id_producto
        where productos.categoria = :cat and detalle.estado= 'pendiente' ");
        $consulta->bindValue(':cat', $categoriaProducto, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "App\Model\Detalle");
    }
    public static function TomarPrimerPendienteDeCategoria($categoriaProducto){
        $objAccesoDatos= AccesoDatos::obtenerInstancia();
        $consulta=$objAccesoDatos->prepararConsulta("SELECT min(detalle.id) as id_detalle, detalle.id_pedido, productos.id as producto_id, productos.descripcion,  productos.tiempo_prom_preparacion, productos.categoria, productos.precio, detalle.estado from detalle
        inner join productos on productos.id = detalle.id_producto
        where productos.categoria=:cat  and detalle.estado = 'pendiente'" );
        $consulta->bindValue(":cat",$categoriaProducto, PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Detalle');
        return $consulta->fetch();
        
    }
    public static function NotificarFinalizacionDeProducto($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.detalle SET estado = 'listo' WHERE (id = :id)");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $r=false;
        if($consulta->rowCount()>0){
            $r=true;
        }
        return $r;
    }
    public static function ObtenerProductosDeCsvString($string){
        $lista=array();
        $k=0;
        for($i=0;$i<strlen($string);$i++){
            if($string[$i]=='-'){
                $lista[count($lista)]=(object) array('id' => substr($string,$k,$i));
                $k=$i;
            }
        }
        if(count($lista)==0){
            $lista=null;
        }
        return $lista;
    }
    public static function GenerarStringDetalleCsv($datos){
        $string=null;
        if($datos!=null){
            $string=$datos[0]->id;
            for($i=1;$i<count($datos);$i++){
            $string=$string ."-". $datos[$i]->id;
            }
        }
        return $string;
    }
    public  static function DeclararListoTodoElDetalle($idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(" UPDATE detalle set estado = 'listo'
        where id_pedido =:idPedido ");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
    }
    public  static function ConsultaTodoDetalleDeUnPedido($codAlfa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
     
        $consulta = $objAccesoDatos->prepararConsulta("SELECT detalle.id, detalle.id_pedido, detalle.id_producto, detalle.estado FROM pedidos inner join detalle on pedidos.id = detalle.id_pedido where pedidos.codigoAlfa = :codigoAlfa and detalle.estado='pendiente';");
        $consulta->bindValue(':codigoAlfa', $codAlfa, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "App\Model\Detalle");
    }
}
